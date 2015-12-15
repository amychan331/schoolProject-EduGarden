<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Confirm if user already login
    if (! empty($session->name)) {
        require_once('model/user.php');
        $user = new User($session);
        $sn = $session->name;
        $user->logged($sn);

        //Display cart content.
        require_once('model/cart.php');
        require_once('view/cart.php');
        $cart = new Cart($user->userName, $session);
        if ($cart->getCid()) {
            $cartPanel = new cartView($cart);
            $cartPanel->addBn();
        } else {
            $boxMsg[] = "You have no items in your carts.";
        }
    } else {
        $boxMsg[] = "Please login first.";
    }

    // Check if there is user input.
    if (isset($_POST["submitQty"])) {
        $cart->input($_POST["productId"], $_POST["cartQtyBox"]);
        if (! $cart->getDifferences()) {
            $boxMsg[] = "Please change the amount before clicking on the submit button.";
        } else if ($_POST["cartQtyBox"] === 0) {
            $boxMsg[] = $cart->delete();
        } else if ($_POST["cartQtyBox"] !== $cart->old) {
            $boxMsg[] = $cart->add();
        } 
    }
?>