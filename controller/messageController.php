<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Output message boxes for not user-only areas or empty carts/inventory.
    if (isset($boxMsg)) {
        foreach($boxMsg as $m) {
            echo "<table class = 'boxMsg'><tr><td><p class='logMsg'>" . $m . "</p></td></tr></table>";
        }
    }

?>