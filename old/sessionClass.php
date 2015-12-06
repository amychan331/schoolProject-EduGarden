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
	private $db;
	private $result;
	private $maxlifetime;
	private $nameMatch;
	private $pwMatch;
	private $current;
	private $newRow;
	private $emptyHistory;
	private $max;

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
		$this->gc($this->maxlifetime);
		$this->db->close();
		return true;
	}

	function read($sid) {
		$this->result = $this->db->query("SELECT data FROM session_holder WHERE id = '${sid}'");
		if ($this->result->num_rows > 0) {
			$names = $this->result->fetch_array();
			echo "<span class = 'err'><p>You are already logged in as user " . $names[0] . ". Please log out first if want to log in as different user.</p></span>";
		}

		return true;
	}

	function write($sid, $data) {;
		if ($this->result->num_rows === 0) {
			// unserialize $_SESSION to get name datas, then reserialized it back.
			if ($data) {	
				$this->current = $_SESSION;
				session_decode($data);
				$data = $_SESSION["data"];
				$data = $this->db->real_escape_string($data);
				$_SESSION = $this->current;
			}

			// before logginn in, make sure the username and password matches. Start by querying database.
			$this->nameMatch = $this->db->query("SELECT data FROM user_info WHERE data = '${data}'") or die("Cannot access database for name matching.");
			$this->pwMatch = $this->db->query("SELECT passWd FROM user_info WHERE data = '${data}'") or die("Cannot access database for password matching.");
			if ($this->nameMatch->num_rows > 0 && $this->pwMatch->num_rows > 0) {
				// since username was found, fetch the user name and password.
				$this->nameMatch = $this->nameMatch->fetch_array();
				$this->pwMatch = $this->pwMatch->fetch_array();
				// check for matching between input and database.
				if ($data == $this->nameMatch[0] && $_POST["passWd"] == $this->pwMatch[0]) {
					// set up variables
					$time = time();
					$login = date("Y-m-d H:i:s");
					// write new row in database.
					$this->newRow = $this->db->query("INSERT INTO session_holder VALUES('${sid}', '${time}', '${data}', '${login}')") or die("Cannot login.");
					if(isset($this->newRow)) {
						echo "<span class = 'logMsg'>Log in successful. Session should last " . $this->maxlifetime/60 . " minutes.";
					}
				} else {
					echo "Username and password does not match.";
				} // Ends matching. Close databases associated with matching.
			}
		}	
		return true;
	}

	function destroy($sid) {
		$this->emptyHistory = $this->db->query("SELECT * FROM session_holder WHERE id = '${sid}'")->num_rows;
		if ($this->emptyHistory === 0) {
			echo "<span class = 'err'>Please log in first.</span>";
		} else {
			$this->db->query("DELETE FROM session_holder WHERE id = '${sid}'") or die("Unable to logout.");
			echo "<span class = 'logMsg'>Logout was successful. See the new Log History here: 
				<a href = " . $_SERVER['REQUEST_URI'] . "> View Log</a></span>";
		}
		return true;
	}

	function gc($max) {
		$old = time() - $max;
		$this->db->query("DELETE FROM session_holder WHERE time < '${old}'") or die("Unable to garbage collect.");
		return true;
	}
}
?>
