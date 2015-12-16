<?php
    require_once("controller/shareController.php");
    require_once("controller/cartController.php");
    require_once("controller/messageController.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>EduGarden: Cart</title>
    <meta charset = "utf-8">
    <meta name = "description" content = "EduGarden: A website for garden and horticulutral education organization to hold a online store.">
    <meta name = "author" content = "Amy Yuen Ying Chan">
    <link rel = "stylesheet" href = "stylesheet.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!--<script src = "https://code.jquery.com/jquery-1.11.3.min.js"></script> -->
    <script src = 'jquery.js'></script>
    <script>
    $(document).ready(function() {
        
        $("span.logMsg").insertAfter("#menu");
        $("span.errMsg").insertAfter("#menu");
        $("table.boxMsg").insertAfter("#menu");
        $("#cartPanel").insertAfter("#menu");
        
        // Search
        $("#submit").click(function() {
            var term = document.getElementById("search").value;
            if (! term) {
                $("div#searchResult").html("Please enter a search term first.");
            } else {
                $.ajax({ 
                    type: 'GET',
                    url: 'model/search.php', 
                    data: "q="+term, 
                    dataType: 'json',
                    success: function(json) { 
                        if (json == "") { 
                            $("div#searchResult").html("Sorry, the search did not matched anything.");
                        } else {
                            $("div#searchResult").html(" ");
                            $(json).each(function(k, v) {
                                $("div#searchResult").append("SKU" + v.pid + ": " + v.productName + " - " + v.sciName + "<br />");
                            });
                        }
                    },
                });
            }
        })

        // For the cart's quantity incremental buttons.
        $("button").on("click", function() {
            var $bn = $(this);
            var oldVal = $bn.parent().find("input").val();

            if ($bn.val() == "+") {
                var newVal = parseFloat(oldVal) + 1;
            } else {
                if (oldVal > 0) {
                    var newVal = parseFloat(oldVal) - 1;
                } else {
                    newVal = 0;
                }
            }
            $bn.parent().find("input").val(newVal);
        })
    })
    </script>
<head>

<body>
    <div id="menu">
        <nav>
            <a href="home.php">Home</a>
            <a href="shop.php" id="shop">Shop</a>
            <a href="dashboard.php" id="dashboard">Log In</a>
        </nav>

        <div id="searchBox"><form action = <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?> method="get">
            <input type="text" id="search">
            <input type="submit" id="submit" class="sub-bn" value="Search" onclick="return false;">
        </form></div>

        <div id="searchResult"></div>
    </div>
</body>
</html>