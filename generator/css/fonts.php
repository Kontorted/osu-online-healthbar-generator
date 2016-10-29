<?php
	session_start();
    
    header("Content-type: text/css; charset: UTF-8");
    
    include "../etc/utilities.php";

    foreach(getFontList() as $font) {
        echo "@font-face {\r\n";
        echo "  font-family: '$font';\r\n";
        echo "  src: url('../fonts/$font.ttf');\r\n";
        echo "}\r\n\r\n";
    }
?>