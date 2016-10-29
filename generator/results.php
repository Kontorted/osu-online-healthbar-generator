<?php
    include "etc/utilities.php";
    session_start();
    $id = session_id();
    
    for($i = 1; $i <= 4; $i++) {
        if(!file_exists(dirname(__FILE__) ."/temp/" . $id . "_" . $i . ".png")) {
            header("Location: /generator");
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>osu! healthbar generator</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Indie+Flower'>
        <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Ubuntu'>
        <link rel="stylesheet" type='text/css' href="css/pure-min.css">
        <link rel="icon" href="images/favicon.ico">
    </head>
    
    <body>
    	<div class="outer">
        <div id="web_body">
            <div id="title">
                <span id="title_span">osu! healthbar generator</span>    
            </div>
            
            <div id="div_form">
                
                <div>
                    <a href="/generator"><button style="float: left" class="pure-button pure-button-primary">&#8592; Back to editor</button></a>
                    <span class="results_1" style="margin-left: -160px">Click an image to download it</span>
                </div>
                
                <br/>
                <br/>
                
                <div>
                    <span style="display: block">scorebar-bg@2x.png</span>
                    <a href="./temp/<?=$id?>_1.png" download="scorebar-bg@2x.png"><img src="./temp/<?=$id?>_1.png" alt="scorebar-bg@2x" class="hd"></a> 
                </div>
                
                <br/>
                
                <div>
                    <span style="display: block">scorebar-colour@2x.png</span>
                 	<a href="./temp/<?=$id?>_2.png" download="scorebar-colour@2x.png"><img src="./temp/<?=$id?>_2.png" alt="scorebar-colour@2x" class="hd"></a> 
                </div>
                
                <br/>
                
                <div>
                    <span style="display: block">scorebar-bg.png</span>
                    <a href="./temp/<?=$id?>_3.png" download="scorebar-bg.png"><img src="./temp/<?=$id?>_3.png" alt="scorebar-bg" class="sd"></a>
                </div>
                
                <br/>
                
                <div>
                    <span style="display: block">scorebar-colour.png</span>
                    <a href="./temp/<?=$id?>_4.png" download="scorebar-colour.png"><img src="./temp/<?=$id?>_4.png" alt="scorebar-colour" class="sd"></a>
                </div>
            </div>
        </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>