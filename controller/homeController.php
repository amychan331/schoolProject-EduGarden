<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Text (text view?)
    // If admin, section to insert text. Connect to homeText mdoel.
    // Banner
    // If admin, section to insert image. Connect to bannerImage mdoel.

?>