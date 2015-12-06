<?php 
// Create User model:
// Create a working user/access model that authenticates users of different types.
// Showing what rights a user has so when client login as different users, client can tell they're different.

// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}
// Get the session handler;
require('sessionHandler.php');

// Begin Class
class User {
	private $userName;
	private $password;
	private $database;
	private $rights = array();

    public function __construct() {
    	define("ADMIN", 1);
		define("CLERK", 2);
		define("USER", 4);
        $this->userName = (isset($_POST['userName'])) ? $_POST['userName'] : 0;
        $this->password = (isset($_POST['passWd'])) ? $_POST['passWd'] : 0 ;
    }

    public function validation() {        
        // Ensure input is not empty
        if (isset($_POST["login"])) {
            if (empty($this->userName) || empty($this->password)) {
                echo "<span class = 'err'>Please fill out both fields.</span>";
                return false;
            } else {
                $session = new Session();
                $_SESSION["data"] = $this->userName;
                return true;
            }
        }
    } // Ends function validation().

    private function setRight() {
    	require('/students/achan123/cs130b/private/dbvar.inc');
    	$this->database = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase) or die("Database not connecting.");
    	unset($dbuser, $dbpass);
    	$this->right = $this->database->query("SELECT userRight FROM user_info WHERE data = '{$this->userName}'") or die("Cannot access database.");
    	$this->right = $this->right->fetch_array();
    	$this->database->close();
    	return $this->right[0];
    }

    public function getRight() {
    	if ($this->setRight() & ADMIN) {
    		$this->rights[] = "admin";
    	}
    	if ($this->setRight() & CLERK) {
    		$this->rights[] = "clerk";
    	}
    	if ($this->setRight() & USER) {
    		$this->rights[] = "regular user";
    	}
    	echo "<span class = 'logMsg'> As " . $this->userName . ", you have ";
    	echo implode(" and ", $this->rights) . " level access.</span>";
    }
    

} //Ends class User.

// check to make sure client is not just revisiting page as login user
if (isset($_SESSION)) {
    echo "Welcome back, " . $_SESSION['data'] . ".";
} else {
	$user = new User();
	if ($user->validation()) {
		$user->getRight();
	}
}
?>

<html>
<head>
      <title>Log-In Page</title>
      <style>
        fieldset {
            padding: 5px; 
            width: 50%;
            margin: auto;

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
              margin-right: 5px;
          }
      </style>
      <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<head>
<body>
    <script>
    $(document).ready(function() {
          $("span").insertAfter("input[name=login]");
    })
      </script>
    <br />
    <form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="post">
        <fieldset>
            <legend>Sign-In Form</legend>
            Name: <input type="text" name="userName" required/><br />
            Password: <input type="password" name="passWd" required/><br />
            <input type="submit" name="login" value="Login" />
        </fieldset>
    </form>
</body>
</html>