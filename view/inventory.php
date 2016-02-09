<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}

class inventoryView {
    private $model;

    public function __construct(Inventory $model) {
        $this->model = $model;
    }

    public function output(){
        echo "<table>";
        echo "<tr><th>SKU</th><th>Availables</th><th>Holds</th><tr/>";
        foreach ($this->model->display() as $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . $cell ."</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    public function addBn() {
        echo "<form action = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " method='post' class='inputForms' >
                <p><span>SKU: <input type='text' id='enterPid' class='inputBox' name='enterPid'></span>
                <span>Amount of new inventory: <input type='text' id='enterQty' class='inputBox' name='enterQty'></span>
                <input type='hidden' name='sumbitted' value='1' />
                <input type='submit' name='addInventory' class='sub-bn' value='Add Inventory' /></p>
            </form>";
    }

    public function delBn() {
        echo "<form action = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " method='post' class='inputForms' >
                <p><span>SKU: <input type='text' id='enterPid' class='inputBox' name='enterPid'><span>
                <span>Amount of removed inventory: <input type='text' id='enterQty' class='inputBox' name='enterQty'></span>
                <input type='hidden' name='sumbitted' value='1' />
                <input type='submit' name='delInventory' class='sub-bn' value='Delete Inventory' /></p>
            </form>";
    }

}
?>