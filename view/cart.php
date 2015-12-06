<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}

class cartView {
    private $model;

    public function __construct(Cart $model) {
        $this->model = $model;
    }

    public function output(){
        echo "<table>";
        echo "<tr><th>SKU</th><th>Name</th><th>Size</th><th>Price</th><th>Quantity</th><tr/>";
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
        echo "<form action = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " method='post'>
                <p>Enter product id: </p>
            </form>";
    }

    public function delBn() {
        echo "<form action = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " method='post'>
                <p>Enter product id: </p>
            </form>";
    }
}
?>