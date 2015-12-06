<?php
    require_once("controller/shareController.php");
    require_once("controller/inventoriesController.php");
    require_once("controller/messageController.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>EduGarden: Inventories</title>
    <meta charset = "utf-8">
    <meta name = "description" content = "EduGarden: A website for garden and horticulutral education organization to hold a online store.">
    <meta name = "author" content = "Amy Yuen Ying Chan">
    <link rel = "stylesheet" href = "stylesheet.css" />

    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!--<script src = "https://code.jquery.com/jquery-1.11.3.min.js"></script> -->
    <script src = 'jquery.js'></script>
    <script>
        $(document).ready(function() {
            $(".inputForms").insertAfter("#menu");
            $("fieldset").insertAfter("#menu");
            $("table").insertAfter("#menu");
            $("span.logMsg").insertBefore("input[name=logout]");
            $("span.errMsg").insertAfter("input[name=login]");
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
                var url = "model/search.php?q="+term;
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

        function changeMenu() {
            var xmlhttp;
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            var url = "model/search.php?q="+term;
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
    </script>
<head>

<body>
    <div id="menu">
        <nav>
            <a href="home.php">Home</a>
            <a href="shop.php" id="shop">Shop</a>
            <a href="login.php">Log In</a>
        </nav>
        <div id="searchResult"></div>
        <div id="searchBox"><form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="get">
            <input type="text" id="search">
            <input type="submit" id="submit" class="sub-bn" value="Search" onclick="searchTerm(); return false;">
        </form></div>
    </div>

</body>
</html>