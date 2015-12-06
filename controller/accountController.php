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

        require_once('model/cart.php');
        require_once('view/cart.php');
        $cart = new Cart($user->userName, $session);
        if ($cart->getCid()) {
            $cartPanel = new cartView($cart);
            $cartPanel->output();
        } else {
            $boxMsg[] = "You have no items in your carts.";
        }
    } else {
        $boxMsg[] = "Please log in to access account page.";
    }
?>