<?php
/*
Write a program which uses:
  1) A database connection.
  2) A session class.
Demonstrate it's working by: 
  1) Storing session (or other) data in the database.
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
		$this->db->close();
		return true;
	}

	function read() {
		$this->result = $this->db->query("SELECT data, login FROM session_holder") or die("Unable to read database.");
		echo '<fieldset>';
		echo '<legend>Log History</legend>';
		echo '<table border = "1">';
		echo '<tr><th>Name</th><th>Login Time</th>';
		while($row = $this->result->fetch_row()) {
			echo '<tr>';
			foreach ($row as $value){ echo '<td>' . $value . '</td>'; }
        	echo '</tr>';
		}
		echo '</table>';
		echo '</fieldset>';
		$this->result->close();
		return true;
	}

	function write($sid, $data) {
		$time = time();
		$login = date("Y-m-d H:i:s");
		if ($data) {
			$current = $_SESSION;
			session_decode($data);
			$data = $_SESSION["data"];
			$data = $this->db->real_escape_string($data);
			$_SESSION = $current;
		}
		$query = $this->db->query("SELECT data FROM session_holder WHERE id = '${sid}'");
		$existRow = $query->num_rows;
		$name = $query->fetch_array();
		if ($existRow > 0) {
			if (empty($_POST["login"])) {
				echo "<span class = 'logMsg'>Welcome back, " . $name[0] ."!</span>";
			} else {
				echo "<span class = 'err'>You have logged in already as " . $name[0] . ".</span>";
			}
		} else if (! empty($data)){
			$this->result = $this->db->query("INSERT INTO session_holder VALUES('${sid}', '${time}', '${data}', '${login}')")
				or die("Cannot login.");
			echo "<span class = 'logMsg'>Log in successful. 
			    Session should last " . $this->maxlifetime/60 . " minutes. 
			    See the new Log History here: <a href = " . $_SERVER['REQUEST_URI'] . "> View Log</a></span>";
		}
		return true;
	}

	function destroy($sid) {
		$emptyHistory = $this->db->query("SELECT * FROM session_holder WHERE id = '${sid}'")->num_rows;
		if ($emptyHistory === 0) {
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

// Process user input:
$session = new Session();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["login"]) && empty($_POST["userName"])) {
		echo "<span class = 'err'>Please enter name.</span>";
	} else if (isset($_POST["logout"]) && empty(session_id())) {
		echo "<span class = 'err'><br />Please log in first.</span>";
	} else if (isset($_POST["logout"])) {
		session_destroy();
	} else if (isset($_POST["userName"])) {
		$name = $_POST["userName"];
		$_SESSION["data"] = $name;
    }
}
?>


<html>
<head>
  	<title>Log-In</title>
  	<style>
  	    table {
  	    	margin: 5px;
  	    }
    	fieldset {
        	padding: 5px; 
        	width: 50%;
    	}
    	legend {
    		font-weight: bold;
    	}
  		.logMsg {
        	color: gray;
        	font-style: italic;
        	margin: 5px 0 5px 0;
        	display: block;
  		}
  		.err{
       		color: red;
        	margin: 5px 0 5px 0;
  		}
  		input {
  			margin-top: 5px;
  		}
  	</style>
  	<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<head>
<body>
    <script>
    $(document).ready(function() {
  		$("span").insertAfter("input[name=logout]");
	})
  	</script>
	<br />
	<form action="a05.php" method="post">
		<fieldset>
    		<legend>Sign-In Form</legend>
    		Name: <input type="text" name="userName" /><br />
    		<input type="submit" name="login" value="Login" />
    		<input type="submit" name="logout" value="Logout" />
    	</fieldset>
	</form>
</body>
</html>