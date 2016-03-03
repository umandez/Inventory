<?php

$title = "Search";

function pullContent ()
{
?>


<html>
<head>
<script>
function showUser(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","test.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>
</head>
<body>

<form>
<input type="text" name="search" value="" placeholder="Enter an Asset Tag here" class="textbox" oninput="showUser(this.value)" />
</form>
<br />
<br />
<div id="txtHint"> ... </div>

</body>
</html>

<?php
}
?>