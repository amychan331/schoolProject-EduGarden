<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
      exit;
}

require_once('model/userControl.php');
class Inventory extends userControl {
    private $session;
    private $inventoryStatus;
    private $qty;
    private $rows = array();

    public function __construct($session) {
        $this->session = $session;
    }

    protected function input($item, $quantity) {
        $this->pid = $item;
        $this->qty = $quantity;

        $this->product = $this->session->db->prepare("SELECT pname, price FROM products WHERE pid = ?");
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

    protected function add() {
        $this->inventory = $this->session->db->prepare("INSERT INTO inventory VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE available += ?");
        $this->inventory->bind_param("iii", $this->pid, $this->qty, $this->qty);
        $this->cart->execute();
    }

    protected function remove() {
        $this->inventory = $this->session->db->prepare("DELETE FROM inventory WHERE $productID = ?");
        $this->inventory->bind_param("i", $this->pid);
        $this->cart->execute();
    }

}
?>
