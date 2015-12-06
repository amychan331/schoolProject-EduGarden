<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
      exit;
}

require_once('model/userControl.php');
class Cart extends userControl {
    private $session;
    private $user;
    private $cartStatus;
    private $cart;
    private $cid;
    private $pid;
    private $qty;
    private $pname;
    private $pz;
    private $rows = array();

    public function __construct($user, $session) {
        $this->user = $user;
        $this->session = $session;
    }

    private function setCid() {
        $this->cartStatus = $this->session->db->prepare("SELECT cartId FROM user_info WHERE data = ?");
        $this->cartStatus->bind_param("s", $this->user);
        $this->cartStatus->execute();
        $this->cartStatus->store_result();
        $this->cartStatus->bind_result($cid);
        $this->cartStatus->fetch();
        if ($cid) {
            $this->cid = $cid;
        }
    }

    public function getCid() {
        $this->setCid();
        return $this->cid;
    }

    protected function input($item, $quantity) {
        $this->pid = $item;
        $this->qty = $quantity;

        $this->products = $this->session->db->prepare("SELECT pname, price FROM products WHERE pid = ?");
        $this->products->bind_param("i", $this->pid);
        $this->products->execute();
        $this->products->store_result();
        $this->products->bind_result($pname, $pz);
        $this->products->fetch();
        if ($pname) {
            $this->pname = $pname;
            $this->pz = $pz;
        } else {
            return FALSE;
        }
    }

    public function display() {
        $this->getCid();
        $this->cart = $this->session->db->prepare("SELECT c.productId, p.productName, p.size, p.price, c.quantity FROM carts c LEFT JOIN products p using (productId) WHERE c.cartId = ?;");
        $this->cart->bind_param("i", $this->cid);
        $this->cart->execute();
        $this->cart->bind_result($pid, $pn, $sz, $price, $qty);
        while($this->cart->fetch()) {
            $this->rows[] = array($pid, $pn, $sz, $price, $qty);
        }
        return $this->rows;
    }

    protected function add() {
        $this->cart = $this->session->db->prepare("INSERT INTO carts WHERE cartId = ?");
        $this->cart->bind_param("iii", $this->cid, $this->pid, $this->qty);
        $this->cart->execute();
        //add to inventory holds, but subtract the corresponding available.
        $this->inventoryChange = $this->session->db->prepare("UPDATE inventory SET available = available - ?, hold = hold + ? WHERE productId = ?");
        $this->cart->bind_param("iii", $this->qty, $this->qty, $this->pid);

    }

    protected function remove() {
        $this->cart = $this->session->db->prepare("DELETE FROM carts WHERE cartId = ?");
        $this->cart->bind_param("iii", $this->cid, $this->pid, $this->qty);
        $this->cart->execute();
        $this->inventoryChange = $this->session->db->prepare("UPDATE inventory SET available = available + ?, hold = hold - ? WHERE productId = ?");
        $this->cart->bind_param("iii", $this->qty, $this->qty, $this->pid);
    }
}
?>