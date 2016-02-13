<?php
require_once('model/userControl.php');
class Purchase extends userControl {
    private $pid;
    private $qty;
    private $oldQty;
    private $session;
    private $user;
    public $cid;
    private $pname;
    private $price;
    private $rows;
    private $cart;
    private $inventory;
    public $logMsg;
    public $errMsg;

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
        } else {
            $this->errMsg[] = "Error. Unable to find user cart.";
        }
    }

    public function getCid() {
        $this->setCid();
    }

    private function itemInCart() {
        $this->cartStatus = $this->session->db->prepare("SELECT quantity FROM carts WHERE productId = ?");
        $this->cartStatus->bind_param("i", $this->pid);
        $this->cartStatus->execute();
        $this->cartStatus->store_result();
        $this->cartStatus->bind_result($oldQty);
        $this->cartStatus->fetch();
        if ($oldQty) {
            $this->oldQty = $oldQty;
        }
    }

    public function input($item, $quantity) {
        $this->pid = intval($item);
        $this->qty = intval($quantity);

        $this->products = $this->session->db->prepare("SELECT productName FROM products WHERE productId = ?");
        $this->products->bind_param("i", $this->pid);
        $this->products->execute();
        $this->products->store_result();
        $this->products->bind_result($pname);
        $this->products->fetch();
        if ($pname) {
            $this->pname = $pname;
            $this->add();
        } else {
            $this->errMsg[] = "Error: Unable to find product.";
        }
    }

    public function display() {
        // Display message to notify customer that item is out and the number of item left in inventory.
        $this->inventory = $this->session->db->prepare("SELECT available FROM inventory WHERE productId = ?");
        $this->inventory->bind_param("i", $this->pid);
        if ($this->inventory->execute()) {
            $this->inventory->store_result();
            $this->inventory->bind_result($available);
            $this->inventory->fetch();
            $this->inventory->close();
            if ($available) {
                $this->boxMsg[] = "We apologize, but we are unable to add to the cart because the item you requested is out. Currently, we have " . $available . " " . $this->pname . " left.";
            } else {
                $this->boxMsg[] = "We apologize, but we are currently out of " . $this->pname . ".";
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function add() {
        //first, make sure input is not negative.
        if($this->qty > 0) {
            $this->itemInCart();

            if($this->oldQty) {
                $this->cart = $this->session->db->prepare("UPDATE carts SET quantity = quantity + ? WHERE productId = ?");
                $this->cart->bind_param("ii", $this->qty, $this->pid);
            } else {
                $this->cart = $this->session->db->prepare("INSERT INTO carts (cartId, productId, quantity) VALUES (?, ?, ?)");
                $this->cart->bind_param("iii", $this->cid, $this->pid, $this->qty);
            } //Ends if($this->oldQty)

            //if cart change is successful, update data to place hold in inventory.
            if($this->cart->execute() && $this->cart->affected_rows > 0) {
                $this->inventory = $this->session->db->prepare("UPDATE inventory SET available = available - ?, hold = hold + ? WHERE productId = ?");
                $this->inventory->bind_param("iii", $this->qty, $this->qty, $this->pid);

                //if inventory change is not successful, undue cart changes and inform the customer.
                if($this->inventory->execute() && $this->inventory->affected_rows > 0) {
                    $this->inventory->close();
                    $this->cart->close();
                    $this->boxMsg[] = "You added "  . $this->qty . " " . $this->pname . " in your cart.";
                } else {
                    $this->delete();
                    $this->display();
                } //Ends if($this->inventory...)

            } else {
                if (! $this->display()) {
                    $this->errMsg[] = "Error: Unable to change cart content. Please contact our team via our contact page. We apologize for any inconvenience.";
                }
            }//Ends ($this->cart...)

        } else {
            $this->boxMsg[] = "Please enter integer input greater than 1.";
        } //Ends if($this->qty > 0)

    }

    public function delete() {
        //use when item is added to cart, but turns out the same item is out in the inventory, thus requiring the program to cancel the cart add.
        $this->cart = $this->session->db->prepare("UPDATE carts SET quantity = quantity - ? WHERE cartId = ? AND productId = ?");
        $this->cart->bind_param("iii", $this->qty, $this->cid, $this->pid);
        $this->cart->execute();
        $this->cart->close();
    }
}
?>