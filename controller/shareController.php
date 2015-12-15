<?php 
    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	    exit;
    }

    // Begin session:
    require_once('model/session.php');
    $session = new Session();

    // Check if there is any form submissions.
    if(isset($_POST['sumbitted'])) {
        header("Location: ". $_SERVER['REQUEST_URI']);
    }

    // Check if user already login:
    if (! empty($session->name) && empty($_POST['logout'])){
        echo "<span class = 'greet'> Welcome, $session->name! </span>";

        // Check user permission
        require_once('model/user.php');
        $user = new User($session);
        $user->logged($session->name);
        $user->getRight();
        if (in_array("admin", $user->rights)) {
            echo "<script>
                    window.onload = function(){
                        document.getElementById(\"shop\").insertAdjacentHTML(\"afterend\", \"<a href='inventories.php'>Inventories</a>\");
                    }
                 </script>";
        }
        if (in_array("user", $user->rights)) {
            echo "<script>
                    window.onload = function(){
                        document.getElementById(\"shop\").insertAdjacentHTML(\"afterend\", \"<a href='cart.php'>Cart</a>\");
                    }
                 </script>";
        }
    }

    // Confirm if there is a search submission, if so, begin searching:
    if (! empty($_GET['q'])) {
        require_once('model/search.php');
        $search = new Search($_GET['q'], $session);
        $search->output();
    }

?>