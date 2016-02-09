<?php 
class productPageView {
    private $model;

    public function __construct(productPage $model, $session, $purchase) {
        $this->model = $model;
        $this->session = $session;
        $this->purchase = $purchase;
    }

    public function output(){
        $itemLink = preg_replace('/\s+/','_', $this->model->display[0]["productName"]);
echo <<< EOD
        <div id = 'container'>
            <h2>{$this->model->display[0]['productName']}</h2><br />
            <h4><em>{$this->model->display[0]['sciName']}</em></h4><br />
            <img alt='{$this->model->display[0]['productName']}' src='itemPages/{$itemLink}/thumbnail.jpg' width='100' height='100' />
            <span id = 'description'> 
                {$this->model->display[0]['description']}<br />
EOD;
                echo "<form action = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " id='purchaseForm' method='POST'>";
                    echo "<input type='hidden' id='item' value='" . $itemLink . "'>";
                        echo "<div id='selection'><select name='selection'>";
                        foreach ($this->model->display as $item => $detail) {
                            echo "<option value='" . $detail["productId"] . "'>" . $detail['size'] . "GAL - $" . $detail['price'] . "</option>";
                        }
                        echo "</select></div>&nbsp";
                    echo "Qty: <input type='text' class='inputBox' name='qty' maxlength='2' size='3'>&nbsp";
                    if (empty($this->session->name)) {
                        echo "<a href ='registration.php'><input type='button' id='register' class='sub-bn' value='Register'></a>";
                    } else {
                        echo "<input type='submit' id='submitPurchase' class='sub-bn' value='Add to Cart'>";
                    }
echo <<< EOD
                </form>
            </span>
        </div>
EOD;
        //total before tax.
    }
}
?>
