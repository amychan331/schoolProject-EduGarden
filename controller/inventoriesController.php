<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Confirm if user already login
    if (! empty($session->name) && in_array("admin", $user->rights)) {
        require_once('model/user.php');
        $user = new User($session);
        $sn = $session->name;
        $user->logged($sn);

        // Display inventory panel.
        require_once('model/inventory.php');
        require_once('view/inventory.php');
        $inventory = new Inventory($session);
        $inventoryPanel = new inventoryView($inventory);
        $inventoryPanel->output();
        $inventoryPanel->addBn();
        $inventoryPanel->delBn();
//        $inventoryPanel->withButtons();
    } else {
        $boxMsg[] = "Only administrative users may access the inventories page.";
    }
?>