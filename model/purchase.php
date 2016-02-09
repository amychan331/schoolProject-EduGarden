<?php
require_once('model/userControl.php');
class Purchase extends userControl {
    private $pid;
    private $qty;
    private $session;
    private $user;
    public $cid;
    private $pname;
    private $price;

    public function __construct($session) {
        $this->session = $session;
        $this->user = $this->session->name;
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
            return TRUE;
        } else {
            return $boxMsg[] = "Error. Unable to find user cart";
        }
    }

    public function getCid() {
        if ($this->setCid()) {
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
            $this->price = $pz;
        } else {
            return FALSE;
        }
    }

    public function display() {
        if (empty($this->cid)) {
            $this->setCid();
        }
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
        //First, make sure input is not negative.
        if ($his->qty > 0) {
            $this->cart = $this->session->db->prepare("UPDATE carts SET quantity = quantity + ? WHERE cartId = ? AND productId = ?");
            $this->cart->bind_param("iii", $this->qty, $this->cid, $this->pid);
            if ($this->cart->execute()) {
                $this->inventory = $this->session->db->prepare("UPDATE inventory SET available = available - ?, hold = hold + ? WHERE productId = ?");
                $this->inventory->bind_param("iii", $this->qty, $this->qty, $this->pid);
                if ($this->inventory->execute()) {
                    return $boxMsg[] = "You added "  . $this->qty . " of " . $this->pname . " in your cart.";
                } else {
                    $this->inventory = $this->session->db->prepare("SELECT available WHERE productId = ?");
                    $this->inventory->bind_param("i", $this->pid);
                    $this->inventory->execute();
                    $this->inventory->store_result();
                    $this->inventory->bind_result($available);
                    $this->inventory->fetch();
                    return $boxMsg[] = "We apologize, but we are out of the number of item you requested. Currently, we have " . $available . " of " . $this->pname . " left.";
                }
            } else {
                return $boxMsg[] = "Error: Unable to change cart content. Please contact the admin via our contact page. We apologize for any inconvenience.";
            }
        } else {
            return $boxMsg[] = "Unable to change quantity of item with SKU " . $this->pid . ". <br /> The input was either less than 1 or an invalid input.";
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