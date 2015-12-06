<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}

// Begins class User:
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
        $this->userName = (isset($_POST['userName'])) ? $_POST['userName'] : 0;
        $this->password = (isset($_POST['passWd'])) ? $_POST['passWd'] : 0 ;
    }

    public function validate() {        
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
            echo "<span class = 'errMsg'>Username and password does not match.</span>";
            return false;
        }
    }  // Ends function authenticate().

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
        echo "<span class = 'logMsg'>" . $this->userName . ", you have " . implode(" and ", $this->rights) . " access.</span>";
    } // Ends function getRight().
} // Ends class User.

// Begins class Cart:
class Cart {
    private $session;
    private $user;
    private $cartStatus;
    private $cart;
    private $rows = array();

    public function __construct($user, $session) {
        $this->user = $user;
        $this->session = $session;
    }

    public function getCart() {
        $this->cartStatus = $this->session->db->prepare("SELECT cartId FROM user_info WHERE data = ?");
        $this->cartStatus->bind_param("s", $this->user);
        $this->cartStatus->execute();
        $this->cartStatus->store_result();
        $this->cartStatus->bind_result($c);
        $this->cartStatus->fetch();
        // check if there are carts associated with user,
        // if yes, get data about it. If none, cart variables remains 0.
        if ($c) {
            $this->cart = $this->session->db->prepare("SELECT * FROM carts WHERE cartId = ?");
            $this->cart->bind_param("i", $c);
            $this->cart->execute();
            $this->cart->bind_result($cid, $pid, $pn, $price, $qty);
            while($this->cart->fetch()) {
                $this->rows[] = array($pid, $pn, $price, $qty);
            }
            return $this->rows;
        } else {
            echo "<table><tr><td><p class='logMsg'>You have no items in your cart.</p></td></tr></table>";
        }
    }
} // Ends class Cart.

// Begins class Inventory:
class Inventory {
    private $session;
    private $user;
    private $inventoryStatus;
    private $quantity;
    private $total;

    public function __construct($session) {
        $this->session = $session;
    }

    public function getInventory() {
        $this->inventoryStatus = $this->session->db->prepare("SELECT quantity FROM carts");
        $this->inventoryStatus->execute();
        $this->inventoryStatus->store_result();
        $this->inventoryStatus->bind_result($q);
        while($this->inventoryStatus->fetch()) {
            $this->quantity[] = $q;
        }
        $this->total = array_sum($this->quantity);
        return $this->total;
    }
} // Ends class Inventory.


// Begins class Search:
class Search {
    private $term;
    private $session;
    private $result;
    public $display;

    public function __construct($term, $session) {
        $this->term = "%{$term}%";
        $this->session = $session;
    }

    public function output() {
        $this->result = $this->session->db->prepare("SELECT productName FROM products WHERE productName LIKE ?");
        $this->result->bind_param("s", $this->term);
        $this->result->execute();
        $this->result->store_result();
        $this->result->bind_result($p);
        while($this->result->fetch()){
            $this->display .= " " . $p;
        }
        if ($this->display) {
            echo $this->display;
        }
    }
} // Ends class Search.
?>