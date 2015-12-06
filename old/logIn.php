<?php 
// Ensure source code is readable:
if (isset($_GET['source'])) {
    highlight_file($_SERVER['SCRIPT_FILENAME']);
	  exit;
}

// Call on required codes:
require('model.php');
require('view.php');

// Begin calling classes:
	$user = new User();
	if ($user->validate()) {
		$user->getRight();
        $cart = new Cart($_SESSION['data'], $user->session);
        if ($cart->getCart()) {
            $cartPanel = new cartView($cart);
            $cartPanel->output();
        }
        if (in_array("admin", $user->rights)) {
            $inventory = new Inventory($user->session);
            $inventoryPanel = new InventoryView($inventory->getInventory());
            $inventoryPanel->output();
        }
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
            $("span").insertAfter("input[name=login]");
        })
        $(document).ready(function() {
            $("table").insertAfter("fieldset");
        })
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

nav a .end {
    position: absolute;
    float: right;
    text-align: right;
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
        <a class = "end"><a href="login.html">Log In</a>
    </nav>
    <br />
    <fieldset>
        <legend>Welcome to Sign-In Form</legend>
        <form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="post">
            Name: <input type="text" name="userName" required/><br />
            Password: <input type="password" name="passWd" required/><br />
            <input type="submit" name="login" value="Login" />
        </form>
        <form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="post">
            <input type="submit" name="logout" value="Logout" />
        </form>
    </fieldset>
</body>
</html>