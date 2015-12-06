<?php 

    // Ensure source code is readable:
    if (isset($_GET['source'])) {
        highlight_file($_SERVER['SCRIPT_FILENAME']);
	   exit;
    }

    // Call on required fiels:
    require('session2.php');
    require('model2.php');
    require('view2.php');


    // Begin session
    $session = new Session();

    if (! empty($session->name)){
        echo "Welcome, $session->name!";
    }

    // Confirm if there is a login submission, if so, begin login process.
    if (isset($_POST['login'])) {
	   $user = new User($session);
	    if ($user->validate()) {
		    $user->getRight();
            $cart = new Cart($_SESSION['data'], $session);
            if ($cart->getCart()) {
                $cartPanel = new cartView($cart);
                $cartPanel->output();
            }
            if (in_array("admin", $user->rights)) {
                $inventory = new Inventory($session);
                $inventoryPanel = new InventoryView($inventory->getInventory());
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
        $search = new Search($_GET['q'], $session);
        $search->output();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log-In Page</title>
    <meta charset="utf-8">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

    <script>
        $(document).ready(function() {
            $("span").insertAfter("input[name=logout]");
        })
        $(document).ready(function() {
            $("table").insertAfter("fieldset");
        })
        function searchTerm() {
            var term = document.getElementById("search").value;
            if (term == "") {
                document.getElementById("searchResult").innerHTML = "No search";
                return;
            } else {
                var xmlhttp;
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                var url = "search.php?q="+term;
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4) {
                        if (xmlhttp.status == 200) {
                            document.getElementById("searchResult").innerHTML = xmlhttp.responseText;
                    
                        }
                    }
                }
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
        }
    </script>

    <style>
/* ***** BODY ***** */
body {
    color: #0B3B17;
    font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
    background-color: #EFFBF2;
    margin: 0;
}
/* ***** NAVIGATION ***** */
nav {
    height: 60px;
    font-size: 20px;
    text-align: left;
    background-color: white;
    clear: both;
}

nav a {
    float: left;
    padding: 20px 10px 20px 10px;
    text-decoration: none;
    color: green;
    margin: 0;
}

nav a:hover {
    color: #585858;
    text-decoration: underline;
}

nav #searchbox
{
    margin: auto;
    top: 12px;
    right:0;
    width: 360px;
    height: 40px;
    overflow: hidden;
    float: right;
    position: absolute;
    clear: both;
}

nav #search {
    background: #f1f1f1;
    margin: auto;
    padding: 0px 6px;
    height: 20px;
    width: 250px;
    border: 1px solid #a4c3ca;
    border-radius: 4px;
    font: normal 14px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) inset, 0 1px 0 rgba(255, 255, 255, 1);            
}
nav .sub-bn {
    background-color: #6cbb6b;
    color: #ffffff;
    font-size: 14px !important;
    font-weight: bold;
    text-shadow: .3px .3px #04B45F;
}
nav .right {
    float: right;
    position: relative;
    right: 0;
}

/* ***** FORM ***** */
fieldset {
    padding: 5px; 
    width: 50%;
    max-width: 300px;
    margin: auto;
}
legend {
    font-weight: bold;
}
form {
    padding: 0;
    margin: 0;
}
input {
    margin-top: 5px;
    margin-right: 5px;
    color: #0A2A12;
}

input.sub-bn {
    background-color: green;
    color: #ffffff;
    font-size: 14px !important;
    font-weight: bold;
    text-shadow: .3px .3px #04B45F;
}
/* ***** TABLES ***** */
table {
    color: green;
    background-color: white;
    border: 2px double gray;
    border-collapse: collapse;
    table-layout: auto;
    position: relative;
    float: center;
    margin: 20px auto 20px auto;
}
tr {
    text-align: left;
}
tr td {
    padding: 5px;
    border: 2px ridge gray;
}
tr th {
    color: #0B3B17;
    text-align: center;
    padding: 5px;
    border: 2px ridge gray;
}

/* ***** buttons ***** */
.sub-bn 
{       
    padding: 3px;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border: .2px solid #04B45F;
    cursor: pointer;
    box-shadow: 0 .2px 0 rgba(255, 255, 255, 0.5) inset;
    -moz-box-shadow: 0 .2px 0 rgba(255, 255, 255, 0.5) inset;
    -webkit-box-shadow: 0 .2px 0 rgba(255, 255, 255, 0.5) inset;
}

.sub-bn:hover {       
    background-color: #95d788;
    background-image: linear-gradient(#6cbb6b, #95d788);
}   

.sub-bn:active {       
    background: #95d788;
    outline: none;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;        
}

.sub-bn::-moz-focus-inner {
       border: 0;  /* Small centering fix for Firefox */
}

/* ***** MESSAGES ***** */
.logMsg {
    color: gray;
    font-style: italic;
    margin: 5px 0 5px 0;
    display: block;
}
.errMsg {
    color: red;
    margin: 5px 0 5px 0;
}
    </style>
<head>

<body>
    <nav>
        <a href="#">Home</a>
        <a href="#">Shop</a>
        <a href="#">Account</a>
        <a href="login.html">Log In</a>
        <p id="searchResult" class="right"></p>
        <form id="searchBox" action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="get">
            <input type="text" id="search">
            <input type="submit" id="submit" class="sub-bn" value="Search" onclick="searchTerm(); return false;">
        </form>
    </nav>
    <br />
    <fieldset>
        <legend>Welcome to Sign-In Form</legend>
        <form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="post">
            Name: <input type="text" name="userName" required/><br />
            Password: <input type="password" name="passWd" required/><br />
            <input type="submit" name="login" class="sub-bn" value="Login" />
        </form>
        <form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="post">
            <input type="submit" name="logout" class="sub-bn" value="Logout" />
        </form>
    </fieldset>
</body>
</html>