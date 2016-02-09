<?php 
    // Check to make sure user haven't already login.
    if (! empty($session->name) && empty($_POST['logout'])){
        $boxMsg[] = "Welcome " . $user->userName . ", you have " . implode(" and ", $user->getRight()) . " access.</span>
        <form action = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " method='post'>
        <input type='submit' id='logging' name='logout' class='sub-bn' value='Logout'/></form>";
        require_once('model/cart.php');
        require_once('view/cart.php');
        $cart = new Cart($user->userName, $session);
        if ($cart->getCid()) {
            $cartPanel = new cartView($cart);
            $cartPanel->output();
        } else {
            $boxMsg[] = "You have no items in your carts.";
        }
            
        if (in_array("admin", $user->rights)) {
            require_once('model/inventory.php');
            require_once('view/inventory.php');
            $inventory = new Inventory($session);
            $inventoryPanel = new inventoryView($inventory);
            $inventoryPanel->output();
        }
    } elseif (isset($_POST['login'])) {
    // Confirm if there is a login submission, if so, begin login process.
        require_once('model/user.php');
	    $user = new User($session);
	    if ($user->validate()) {
            //Flag login is successful.
            $_GET['logged'] = true;
            return true;
        } else {
            require_once('view/login.php');
            $loginPanel = new loginView();
            $loginPanel->output();
        }
    } else {
        require_once('view/login.php');
        $loginPanel = new loginView();
        $loginPanel->output();
    }

    // Confirm if there is a logout submission, if so, begin logout process.
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
    }


?>