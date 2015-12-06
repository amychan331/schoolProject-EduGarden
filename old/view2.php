<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}

class cartView {
	private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function output(){
        echo "<table>";
        echo "<tr><th>SKU</th><th>Name</th><th>Price</th><th>Quantity</th><tr/>";
    	foreach ($this->model->getCart() as $row) {
    		echo "<tr>";
    		foreach ($row as $cell) {
    			echo "<td>" . $cell ."</td>";
    		}
    		echo "</tr>";
    	}
    	echo "</table>";
    }
}


class inventoryView {
    private $holds;

    public function __construct($holds) {
        $this->holds = $holds;
    }

    public function output(){
        echo "<table><tr><td><p class='logMsg'>Currently, there are " . $this->holds . " items on hold in customer carts.</p></td></tr></table>";
    }
}

?>