<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Confirm if user already login
    if (! empty($session->name)) {
        //Display cart content.
        require_once('model/cart.php');
        require_once('view/cart.php');
        $cart = new Cart($user->userName, $session);
        if (! $_POST) {
            if ($cart->getCid()) {
                $cartPanel = new cartView($cart);
                $cartPanel->addBn();
            } else {
                return $boxMsg[] = "You have no items in your carts.";
            }
        }
    } else {
        return $boxMsg[] = "Please login first.";
    }

    // Check if there is user input.
    if (isset($_POST["submitQty"])) {
        $cart->getCid();
        $cart->input($_POST["productId"], $_POST["cartQtyBox"]);
        if ($cart->getDifferences() === false) {
            return $boxMsg[] = "Please change the amount before clicking on the submit button.";
        } else if ($_POST["cartQtyBox"] === 0) {
            return $boxMsg[] = $cart->delete(); // deletes the sku from cart completely.
        } else {
            return $boxMsg[] = $cart->add(); // while the name is "add", it really indicate changes to amount that don't delete sku from cart.
        } 
    }
?>