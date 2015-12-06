<?php
/*
This program which uses:
  1) A database connection.
  2) A session class.
It's work by: 
  1) Storing session data in the database.
  2) Retrieving the session data.
*/
// Ensure source code is readable:
if (isset($_GET['source'])) {
	highlight_file($_SERVER['SCRIPT_FILENAME']);
	exit;
}

// A Session class:
class Session {
	public $db;
	private $result;
	public $name;
	private $maxlifetime;
	private $max;
	private $current;
	private $emptyHistory;

	public function __construct() {
		session_set_save_handler(
			array($this, 'open'), 
			array($this, 'close'), 
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);
		ini_set('session.gc_maxlifetime', 900);
		$this->maxlifetime = ini_get('session.gc_maxlifetime');
		session_start();
	}

	function open() {
		require_once('/students/achan123/cs130b/private/dbvar.inc');
		$this->db = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase) or die("Database not connecting.");
		unset($dbuser, $dbpass);
		return true;
	}

	function close() {
		echo "closing session handler...";
		$this->db->close();
		return true;
	}

	function read($sid) {
		// Make sure that any expired login sesion are already garbage collected.
		echo "attempting gc...";
		$this->gc($this->maxlifetime);
		// Make sure that user is not already in a valid login session. If so, close database and return false.
		echo "attempting reading...";
		$this->result = $this->db->prepare("SELECT data FROM session_holder WHERE id = ?;");
		$this->result->bind_param("s", $sid);
		$this->result->execute();
		$this->result->store_result();
		if ($this->result->num_rows > 0) {
			$this->names = $this->result->fetch();
			echo "<span class = 'errMsg'><br />You are already logged in as user " . $this->names[0] . ". Please log out first if want to log in as different user.</span>";
			return false;
		};
		return true;
	}

	function write($sid, $data) {
		echo "attempting writing...";
		if ($data) {	
			// unserialize $_SESSION to get name datas, then reserialized it back.
			$this->current = $_SESSION;
			session_decode($data);
			$data = $_SESSION["data"];
			$data = $this->db->real_escape_string($data);
			$_SESSION = $this->current;
			// set up variables
			$time = time();
			$login = date("Y-m-d H:i:s");
			// write new row in database.
			$this->result = $this->db->prepare("INSERT INTO session_holder VALUES(?, ?, ?, ?)");
			$this->result->bind_param("siss", $sid, $time, $data, $login);
			$this->result->execute();
			echo "<span class = 'logMsg'>Log in successful. Session should last " . $this->maxlifetime/60 . " minutes.</span>";
			$this->result->close();
			return true;
		}

	}	

	function destroy($sid) {
		echo "attempting destroy...";
		$this->emptyHistory = $this->db->query("SELECT * FROM session_holder WHERE id = '${sid}'")->num_rows;
		if ($this->emptyHistory === 0) {
			$GLOBAL['errMsg'] = "Please log in first.";
		} else {
			$this->result = $this->db->prepare("DELETE FROM session_holder WHERE id = ?");
			$this->result->bind_param("s", $sid);
			$this->result->execute();
			echo "<span class = 'logMsg'>Logout was successful.</span>";
			$this->result->close();
		}
		return true;
	}

	function gc($max) {
		echo "gc starting...";
		$old = time() - $max;
		$this->result = $this->db->prepare("DELETE FROM session_holder WHERE time < ?");
		$this->result->bind_param("d", $old);
		if ($this->result) {
			$this->result->execute();
			$this->result->close();
		}
		return true;
	}
}
?>
