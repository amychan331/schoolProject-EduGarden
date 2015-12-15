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

        // Display inventory panel
        require_once('model/inventory.php');
        $inventory = new Inventory($session);
        require_once('view/inventory.php');
        $inventoryPanel = new inventoryView($inventory);
        $inventoryPanel->output();
        $inventoryPanel->addBn();
        $inventoryPanel->delBn();

        // Check for inventory panel submissions
        if (isset($_POST['addInventory'])) {
            if (! $_POST['enterQty']) {
                $boxMsg[] = "Please enter an amount first.";
            } else {
                $inventory->input($_POST['enterPid'], $_POST['enterQty']);
                $inventory->add();
            }
        }
        if (isset($_POST['delInventory'])) {
            if (! $_POST['enterQty']) {
                $boxMsg[] = "Please enter an amount first.";
            } else {
                $inventory->input($_POST['enterPid'], $_POST['enterQty']);
                $inventory->delete();
            }
        }
    } else {
        $boxMsg[] = "Accessible only by Admins. Please login first.";
    }
?>