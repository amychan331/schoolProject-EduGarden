function searchTerm() {
    if (str == "") {
        document.getElementById(str).innerHTML += "";
        return;
    } else { 
        var xmlhttp;
//        var data_file = "test.json";
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status == 200) {
                    document.getElementById('searchResult').innerHTML = xmlhttp.responseText
                    
                }
            }
        }
        var term = document.getElementById("submit").innerHTML.value;
        xmlhttp.open("GET", "login.php?q=" + term, true);
        xmlhttp.send();
    }
}
