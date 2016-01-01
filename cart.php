<?php
if (isset($_POST['submitQty'])) {
    require_once('model/session.php');
    $session = new Session();
    require_once('model/user.php');
    $user = new User($session);
    $user->logged($session->name);
    $user->getRight();
    require_once("controller/cartController.php");
    if (isset($boxMsg)) {
        foreach($boxMsg as $m) {
            $logMsg[] = array($m);
            echo json_encode($logMsg);
        }
    }
    die;
} else {
    require_once("controller/shareController.php");
    require_once("controller/cartController.php");
    require_once("controller/messageController.php");
}
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

        // For cart's new quantity submissions.
        $("#cartPanel").submit(function(e) {
            e.preventDefault();
            var $form = $(this),
                f_pid = $form.find("input[name='productId']").val(),
                f_qty = $form.find("input[name='cartQtyBox']").val();
            
            var posting = $.post($form.attr("action"), { submitQty: true, productId: f_pid, cartQtyBox: f_qty }, null, "json");
            posting.done(function (postData) { //postData does the submission & gets the resulted boxMsg
                var getting = $.get("model/cartPanel.php", { submitQty: true });
                getting.done(function (getData) { //getData receives the newest resulted quality by loading a new page.
                    var g_pid = getData.pid,
                    g_qty = getData.qty;
                    $("input#"+g_pid).parent().find("#cartQtyBox").val(g_qty);
                });
                if ($(".boxMsg")){ //Prevents duplicate boxMsg with new submissions.
                    $(".boxMsg").remove(); 
                }
                $("#cartPanel").after("<table class = 'boxMsg'><td><p class='logMsg'></p></td></table>");
                $.each(postData, function(k,v) { //In case there are more than one messages.
                    $("table.boxMsg p").append(v);
                });
            });
        });

    })
    </script>
</head>

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