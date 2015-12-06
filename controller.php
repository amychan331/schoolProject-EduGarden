<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Begin session
    require_once('model/session.php');
    $session = new Session();

    if (! empty($session->name)){
        echo "<span class = 'greet'> Welcome, $session->name! </span>";
    }

    // Confirm if there is a login submission, if so, begin login process.
    if (isset($_POST['login'])) {
        require_once('model/user.php');
	    $user = new User($session);
	    if ($user->validate()) {
		    $user->getRight();
            require_once('model/cart.php');
            require_once('view/cart.php');
            $cart = new Cart($_SESSION['data'], $session);
            if ($cart->getCid()) {
                $cartPanel = new cartView($cart);
                $cartPanel->output();
            }
            if (in_array("admin", $user->rights)) {
                require_once('model/inventory.php');
                require_once('view/inventory.php');
                $inventory = new Inventory($session);
                $inventoryPanel = new inventoryView($inventory);
                $inventoryPanel->output();
            }
        }
    }
    // Confirm if there is a logout submission, if so, begin logout process.
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
    }

    // Confirm if there is a search submission, if so, begin searching.
    if (! empty($_GET['q'])) {
        require_once('model/search.php');
        $search = new Search($_GET['q'], $session);
        $search->output();
    }

?>