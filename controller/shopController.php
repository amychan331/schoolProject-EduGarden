<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    require_once("controller/shopController.php");
    // Call on selectFilter view and model.
    // Call on productSquare view (which should output both image and subtext).
?>