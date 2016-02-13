<?php
    require_once("controller/shareController.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop<?php if(isset($_GET['item'])) { echo ": " . preg_replace('/_/',' ', $_GET['item']); } ?></title>
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
        $(".boxMsg").insertAfter("#purchaseForm");
        // Search
        $("#submit").click(function(e) {
            e.preventDefault();
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
    })
    </script>
    <noscript><p id="jsMsg">This site strive its best to accomedate all users, but turning on Javascript will enable the best experience.</p></noscript>
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
            <input type="submit" id="submit" class="sub-bn" value="Search">
        </form></div>

        <div id="searchResult"></div>
    </div>
</body>
</html>
<?php
    require_once("controller/shopController.php");
    require_once("controller/messageController.php");
?>