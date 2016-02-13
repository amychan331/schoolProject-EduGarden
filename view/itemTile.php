<?php 
class itemTileView {
    private $model;

    public function __construct(itemTile $model) {
        $this->model = $model;
    }

    public function output(){
        echo "<div id='tileContainer'>";
        foreach ($this->model->display as $item => $detail) {
            $itemLink = preg_replace('/\s+/','_', $item);
            echo "<div class= 'tileSpace'>";
            echo "<a href='?item=" . $itemLink ."'>";
            echo "<img alt='" . $item . "' src='itemPages/" . $itemLink . "/thumbnail.jpg' width='100' height='100'>";
            echo "<p>" . $item . ": " . $detail["sciName"][0] . "</p>";
            echo "</a>";
            if (count($detail["price"]) > 1) { //since some items have different prices due to size
                echo "<p>" . min($detail["price"]). " - " . max($detail["price"]). "</p>"; 
            } else {
                echo "<p>" . $detail["price"][0] . "</p>"; 
            }
            echo "</div>";
        }
        echo "</div>";
    }
}
?>
