<?php
    include "etc/utilities.php";
    session_start();
    $id = session_id();
    
    if(!file_exists(dirname(__FILE__) ."/temp/" . $id . "_0.png")) {
        header("Location: /generator");
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
                    <span id="preview_text" style="margin-left: 212px" class="results_1">Preview:</span>
                    <button id="lights" class="pure-button" style="float:right;">Toggle black background</button>  
                </div>
                
                <div id="errors" class="errors">
                    <?php 
                       if(isset($_SESSION['errors'])) {
                           foreach($_SESSION['errors'] as $error) {
                               echo "<p>" . $error . "</p>";
                           }
                           unset($_SESSION['errors']);
                       }
                    ?>
                </div>
                
                <br/>
                <br/>
                
                <div>
                    <img src="./temp/<?=$id?>_0.png" alt="scorebar-bg@2x" class="hd">
                </div>
                
                <br/>
                <br/>
                
                <div>
                    <a href="/generator/results"><button class="pure-button pure-button-primary">Looks good!</button></a>
                    &nbsp;&nbsp;
                    <a href="/generator"><button class="pure-button">Go back to editor</button></a>
                </div>
                
            </div>
        </div>
        </div>
        <?php include 'footer.php'; ?>
        
        <script>
            var lights = true;
            $('#lights').click(function() {
                if(lights) {
                    lights = false;
                    $('#preview_text').css('color', 'white');
                    $('#div_form').css('background-color', '#000000');
                } else {
                    lights = true;
                    $('#preview_text').css('color', '');
                    $('#div_form').css('background-color', '');
                }
            });
        </script>
    </body>
</html>