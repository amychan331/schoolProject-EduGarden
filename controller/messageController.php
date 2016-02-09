<?php 

    // Output message boxes for not user-only areas or empty carts/inventory.
    if (isset($boxMsg)) {
        foreach($boxMsg as $m) {
            echo "<table class = 'boxMsg'><td><p class='logMsg'>" . $m . "</p></td></table>";
        }
    }

    checkLog();
    ob_end_flush();

?>