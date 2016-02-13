<?php 
// Ensure source code is readable:
require_once('model/userControl.php');
class Inventory extends userControl {
    private $session;
    private $inventoryStatus;
    private $qty;
    private $rows = array();

    public function __construct($session) {
        $this->session = $session;
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
            echo "<table><tr><td><p class='logMsg'>Pid not found. Such product does not exist in store.</p></td></tr></table>";
        }
    }

    public function display() {
        $this->inventoryStatus = $this->session->db->prepare("SELECT * FROM inventory");
        $this->inventoryStatus->execute();
        $this->inventoryStatus->store_result();
        $this->inventoryStatus->bind_result($pid, $available, $hold);
        while($this->inventoryStatus->fetch()) {
            $this->rows[] = array($pid, $available, $hold);
        }
        return $this->rows;
    }

    public function add() {
        $this->inventory = $this->session->db->prepare("UPDATE inventory SET available = available + ? WHERE productId = ?");
        $this->inventory->bind_param("ii", $this->qty, $this->pid);
        $this->inventory->execute();
    }

    public function delete() {
        $this->inventory = $this->session->db->prepare("UPDATE inventory SET available = available - ? WHERE productId = ?");
        $this->inventory->bind_param("ii", $this->qty, $this->pid);
        $this->inventory->execute();
    }

}
?>
