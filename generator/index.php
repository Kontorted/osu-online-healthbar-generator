<?php
    
    include "etc/utilities.php";
    
	session_start();
	
	if(!isset($_SESSION['form'])) {
		$username = "";
		$text = "";
		$useFlag = false;
		$oldFlags = false;
		$font = "pneumati";
		$fontsize = 20;
        $colorTheme = "";
		$color1 = "#2B5F74";
		$color2 = "#65C5CE";
		$color3 = "#1B6788";
		$color4 = "#2DA2D3";
		$color5 = "#FFFFFF";
		$square = false;
	} else {
		extract($_SESSION['form']);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>osu! healthbar generator</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Indie+Flower'>
		<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Ubuntu'>
		<link rel="stylesheet" type='text/css' href="css/pure-min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/loading.css">
		<link rel="stylesheet" type="text/css" href="css/fonts.php">
		<link rel="icon" href="images/favicon.ico">
	</head>
	
	<body>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-45596469-4', 'auto');
			ga('send', 'pageview');
		</script>
		<div class="outer">
    	<div id="web_body">
    	   <div id="title">
    	       <span id="title_span">osu! healthbar generator</span>
    	   </div>
    		
    		<div id="div_form">
    			<a href="advanced" class="pure-button" style="float:right;">Advanced options</a>
    			
	    		<div id="errors" class="errors"><?php 
	    	           if(isset($_SESSION['errors'])) {
	    	               foreach($_SESSION['errors'] as $error) {
	    	                   echo "<p>" . $error . "</p>";
	    	               }
	                       unset($_SESSION['errors']);
	                   }
				?></div>
	    	    
	    	    <?php if(!isset($_SESSION['brver'])) {
	    	        echo "<div id='brver' class='errors'></div>";
                    $_SESSION['brver'] = true;
	    	    } ?>
				<form id="form_data" action="generator" method="post" onsubmit="return checkForm()">
        	        <div>
            	        <table id="formTable1">
            	            <tr>
            	               <td><label for="username">Your osu! username:</label></td> 
            	               <td><input type="text" name="username" id="username" value="<?=$username?>"></td>
            	            </tr>
                    
                            <tr>
                                <td><label for="text">Text to print:</label> </td>
                                <td><input type="text" name="text" id="text" value="<?=$text?>" placeholder="leave blank to print username"></td>
                            </tr>
                	        
                	        <tr>
                	            <td><label for="useFlag">Add country flag?</label></td>
                	            <td><input type="checkbox" name="useFlag" id="useFlag" <?php if($useFlag) echo 'checked'; ?>></td>
                	        </tr>
                	        
                	        <tr>
                	            <td><label for="oldFlags">Use old flags?</label></td>
                	            <td><input type="checkbox" name="oldFlags" id="oldFlags" <?php if($oldFlags) echo 'checked'; ?> disabled></td>
                	        </tr>
                	        
                	        <tr>
                	            <td><label for="shape">Avatar shape:</label></td>
                	            <td>
                	                <select id="shape" name="shape">
                	                   <option value="shape_circle" <?=!$square?'selected':''?>>Circle</option>
                	                   <option value="shape_square" <?=$square?'selected':''?>>Square</option>
                	               </select>
                	            </td>
                	        </tr>
                        </table>
                    </div>
                    
                    <br/>
                    
                    <div>
                        <table id="formTable2">
                            <tr>
                                <td><label for="font">Font:</label></td>
                                <td><label for="fontsize">Font size:</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <select style="font-family: '<?=$font?>'; font-size: 200%;" name="font" id="font">
                                        <?php foreach(getFontList() as $fontname) { ?>
                                           <option style="font-family: '<?=$fontname?>'; font-size: 100%;" value="<?=$fontname?>" <?=$fontname==$font?'selected':''?>>The quick brown fox jumps over the lazy dog</option>
                                         <?php } ?>
                                    </select>
                                </td>
                                <td><input type="number" name="fontsize" id="fontsize" min="1" max="100" style="width: 50px" value="<?=$fontsize?>"></td>
                             </tr>
                        </table>
                    </div>
                   
                   <br/>
                   
                   <div>
                       <table id="formTable3">
                           <tr>
                               <td><label for="colorTheme">Color theme:</label></td>
                               <td><label for="color1">Background color:</label></td>
                               <td><label for="color2">Foreground color:</label></td>
                               <td><label for="color3">Font color:</label></td>
                               <td><label for="color4">Avatar border color:</label></td>
							   <td><label for="color5">Healthbar border color:</label></td>
                           </tr>
                           
                           <tr>
                               <td class="t3d1">
                                    <select id="colorTheme" name="colorTheme">
                                        <option value="Blue" <?=$colorTheme=='Blue'?'selected':''?>>Blue</option>
                                        <option value="Red" <?=$colorTheme=='Red'?'selected':''?>>Red</option>
                                        <option value="Yellow" <?=$colorTheme=='Yellow'?'selected':''?>>Yellow</option>
                                        <option value="Green" <?=$colorTheme=='Green'?'selected':''?>>Green</option>
                                        <option value="Orange" <?=$colorTheme=='Orange'?'selected':''?>>Orange</option>
                                        <option value="Purple" <?=$colorTheme=='Purple'?'selected':''?>>Purple</option>
                                        <option value="Pink" <?=$colorTheme=='Pink'?'selected':''?>>Pink</option>
                                        <option value="Brown" <?=$colorTheme=='Brown'?'selected':''?>>Brown</option>
                                        <option value="White" <?=$colorTheme=='White'?'selected':''?>>White</option>
                                    </select>
                                    <button class="pure-button" type="button" id="colorThemeUpdate">Apply</button>
                               </td>
                               <td class="t3d2"><input type="color" name="color1" id="color1" class="color_picker" value="<?=$color1?>"></td>
                               <td class="t3d2"><input type="color" name="color2" id="color2" class="color_picker" value="<?=$color2?>"></td>
                               <td class="t3d2"><input type="color" name="color3" id="color3" class="color_picker" value="<?=$color3?>"></td>
                               <td class="t3d2"><input type="color" name="color4" id="color4" class="color_picker" value="<?=$color4?>"></td>
							   <td class="t3d2"><input type="color" name="color5" id="color5" class="color_picker" value="<?=$color5?>"></td>
                           </tr>
                       </table>
                   </div>
                   
                   <br/>
                   
                   <div style="height: 45px;">
                       <button class='pure-button pure-button-primary' id="submit" type="submit">Go!</button>
                       <div id="loading" style="display: none" class="cssload-loader"></div>
                   </div>
                    
        	    </form>
    	    </div>
    	</div>
    	</div>
    	
    	<?php include 'footer.php'; ?>
    	<script src="js/scripts_index.js"></script>
	</body>
</html>