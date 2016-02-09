<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}

class cartView {
    private $model;
    public $bn;

    public function __construct(Cart $model) {
        $this->model = $model;
    }

    public function output(){
        echo "<table>";
        echo "<tr><th>SKU</th><th>Name</th><th>Size</th><th>Price</th><th>Quantity</th><tr/>";
        foreach ($this->model->display() as $row) {
            echo "<tr class='cartRows'>";
            foreach ($row as $cell) {
                echo "<td>" . $cell ."</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    public function addBn() {
        echo "<form id='cartPanel' action='" . htmlspecialchars($_SERVER['REQUEST_URI']) . "'>";
        echo "<table>";
        echo "<tr><th>SKU</th><th>Name</th><th>Size</th><th>Price</th><th>Quantity</th><tr/>";
        foreach ($this->model->display() as $row) {
            echo "<tr class='cartRows'>";
            $i = 1;
            foreach ($row as $cell) {
                if ($i === 1) {
                    echo "<input type='hidden' name='productId' id='" . $cell . "' value='" . $cell . "'>";
                    echo "<td>" . $cell ."</td>";
                    $i++;
                } else if ($i <= 4) {
                    echo "<td>" . $cell ."</td>";
                    $i++;
                } else {
                    echo "<td><button type='button' class='cartQtyBn' data-type='plus' value='+'><i class='fa fa-plus fa-sm'></i></button>";
                    echo "<input type='text' id='cartQtyBox' class='inputBox' name='cartQtyBox' size='3' value='" . floatval($cell) . "'>";
                    echo "<button type='button' class='cartQtyBn' data-type='minus' value='-'><i class='fa fa-minus fa-sm'></i></button></td>";
                    $i = 0;
                }
            }
            echo "</tr>";
        }
        echo "<tr><td colspan='5'><input type='submit' id='submitQty' name='submitQty' class='sub-bn' value='Submit'></td></tr></table>";
        echo "</form>";
    }
}
?>
