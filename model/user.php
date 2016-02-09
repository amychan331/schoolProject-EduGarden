<?php 

class User {
    private $session;
	public $userName;
	private $password;
	private $match;
    private $right;
	public $rights = array();

    public function __construct($session) {
    	define("ADMIN", 1);
		define("CLERK", 2);
		define("USER", 4);
        $this->session = $session;
    }

    public function validate() {
        $this->userName = (isset($_POST['userName'])) ? $_POST['userName'] : 0;
        $this->password = (isset($_POST['passWd'])) ? $_POST['passWd'] : 0 ;
        if (empty($this->userName) || empty($this->password)) {
            echo "<span class = 'errMsg'>Please fill out both fields.</span>";
            return false;
        } else  if (! isset($this->session->names) && $this->authenticate()) {
            $_SESSION["data"] = $this->userName;
            return true;
        } else {
            // both input field was filled, name was found in session, but authentication failed. Validation failed.
            return false;
        }
    } // Ends function validation().

    private function authenticate() {
        $this->match = $this->session->db->prepare("SELECT data, passWd FROM user_info WHERE data = ? AND passWd = ?");
        $this->match->bind_param("ss", $this->userName, $this->password);
        $this->match->execute();
        $this->match->store_result();
        $this->match->bind_result($u, $p);
		if ($this->match->num_rows > 0) {
			// since username and password was found, return true.
			return true;
		} else {
            echo "<br /><span class = 'errMsg'>Username and password does not match.</span>";
            return false;
        }
    }  // Ends function authenticate().

    public function logged($sessionName) {
        $this->userName = $sessionName;
    } // Ends function logged().

    private function setRight() {
        $this->right = $this->session->db->prepare("SELECT userRight FROM user_info WHERE data = ?");
        $this->right->bind_param("s", $this->userName);
        $this->right->execute();
        $this->right->store_result();
        $this->right->bind_result($r);
        $this->right->fetch();
    	return $r;
    } // Ends function setRight().

    public function getRight() {
    	if ($this->setRight() & ADMIN) {
    		$this->rights[] = "admin";
    	}
    	if ($this->setRight() & CLERK) {
    		$this->rights[] = "clerk";
    	}
    	if ($this->setRight() & USER) {
    		$this->rights[] = "user";
    	}
        return $this->rights;
    } // Ends function getRight().
}
?>