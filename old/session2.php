
<?php
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
		register_shutdown_function('session_write_close');
		ini_set('session.gc_maxlifetime', 900);
		$this->maxlifetime = ini_get('session.gc_maxlifetime');
		ini_set('session.save_path', __DIR__ . '/sessions');
		echo session_save_path();
		session_set_cookie_params($this->maxlifetime);
		session_start();
	}
	function open() {
		require_once('/students/achan123/cs130b/private/dbvar.inc');
		$this->db = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase) or die("Database not connecting.");
		unset($dbuser, $dbpass);
		return true;
	}
	function close() {
		var_dump($_SESSION);

		$this->db->close();
		return true;
	}
	function read($sid) {
		$this->result = $this->db->prepare("SELECT data FROM session_holder WHERE id = ?");
		$this->result->bind_param("s", $sid);
		$this->result->execute();
		$this->result->store_result();
		$this->result->bind_result($this->name);
		if ($this->result->num_rows > 0) {
			$this->result->fetch();
		}
		return true;
	}
	function write($sid, $data) {
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
		$this->emptyHistory = $this->db->query("SELECT * FROM session_holder WHERE id = '${sid}'")->num_rows;
		if ($this->emptyHistory === 0) {
			echo "<span class = 'errMsg'>Please log in first.</span>";
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