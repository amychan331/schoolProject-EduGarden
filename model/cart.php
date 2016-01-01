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
    private $products;
    private $oldCart;
    private $cart;
    private $inventory;
    public $old;
    private $diff;
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
        if (isset($cid)) {
            $this->cid = $cid;
            return 1;
        } else {
            return $boxMsg[] = "Error. Unable to find user cart";
        }
    }

    public function getCid() {
        if ($this->setCid() === 1) {
            return $this->cid;
        }
    }

    public function input($item, $quantity) {
        $this->pid = intval($item);
        $this->qty = intval($quantity);

        $this->products = $this->session->db->prepare("SELECT productName, price FROM products WHERE productId = ?");
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

    public function add() {
        //add to inventory holds, but subtract the corresponding available.
        $this->inventory = $this->session->db->prepare("UPDATE inventory SET available = available - ?, hold = hold + ? WHERE productId = ?");
        $this->inventory->bind_param("iii", $this->diff, $this->diff, $this->pid);
        if ($this->inventory->execute()) {
            $this->cart = $this->session->db->prepare("UPDATE carts SET quantity = ? WHERE cartId = ? AND productId = ?");
            $this->cart->bind_param("iii", $this->qty, $this->cid, $this->pid);
            $this->cart->execute();
            if ($this->diff > 0) {
                return $boxMsg[] = "You added "  . $this->diff . " of item with SKU " . $this->pid . " in your cart.";
            } else {
                return $boxMsg[] = "You reduce "  . abs($this->diff) . " of item with SKU " . $this->pid . " in your cart.";
            }
        } else {
            return $boxMsg[] = "Unable to change quantity of item with SKU " . $this->pid . ". <br /> Either you have requested a negative amount of hold or the item is out.";
        }

    }

    public function delete() {
        $this->inventory = $this->session->db->prepare("UPDATE inventory SET available = available + ?, hold = hold - ? WHERE productId = ?");
        $this->inventory->bind_param("iii", $this->oldQty, $this->oldQty, $this->pid);
        $this->inventory->execute();
        $this->cart = $this->session->db->prepare("DELETE FROM carts WHERE cartId = ? AND productId =?");
        $this->cart->bind_param("iii", $this->cid, $this->pid);
        $this->cart->execute();
        return $boxMsg[] = "You removed item with SKU " . $this->pid . " from your cart.";
    }

    public function getDifferences() {
        $this->oldCart = $this->session->db->prepare("SELECT quantity FROM carts WHERE productId = ? AND cartId = ?");
        $this->oldCart->bind_param("ii", $this->pid, $this->cid);
        $this->oldCart->execute();
        $this->oldCart->bind_result($this->old);
        $this->oldCart->fetch();
        $this->oldCart->close();

        $this->diff = $this->qty - $this->old;
        if ($this->diff !== 0) {
            return TRUE;
        } else {
            return FALSE;
        }

    }
}
?>