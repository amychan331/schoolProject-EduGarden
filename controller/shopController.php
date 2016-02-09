<?php 
    // Check if there is a request to see product detail.
    if (isset($_GET['item'])) {
        require_once('model/productPage.php');
        require_once('view/productPage.php');
        $product = new productPage($_GET['item'], $session);
        $product->setContent();
        $purchase = null;
        // Check for purchase request
        if (isset($_POST['qty'])) {
            require_once('model/purchase.php');
            $purchase = new Purchase($session);
            $purchase->getCid();
            //$purchase->input($_POST['selection'], $_POST['qty']);
        }
        $productView = new productPageView($product, $session, $purchase);
        $productView->output();


    }
    // Call on selectFilter view and model.
    else if (isset($_GET['filtered'])) {

    }
    // Call on itemTile view (which should output both image and subtext).
    else {
        require_once('model/itemTile.php');
        require_once('view/itemTile.php');
        $tile = new itemTile($session);
        $tile->setContent();
        $tilePanel = new itemTileView($tile);
        $tilePanel->output();
    }

?>