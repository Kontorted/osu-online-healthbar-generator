<?php
    
    include "etc/utilities.php";
    
	session_start();
	
	if(!isset($_SESSION['advanced'])) {
        $proportions = false;
        $flagOverride = false;
        $flagOverrideData = "af";
        $imageOverride = false;
        $imageOverrideFileName = "";
        $noAvatar = false;
	} else {
		extract($_SESSION['advanced']);
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
		<link rel="stylesheet" type="text/css" href="css/tooltipster.css">
		<link rel="stylesheet" type="text/css" href="css/loading.css">
		<script src="js/jquery.tooltipster.min.js"></script>
		<script src="js/countrycodes.js"></script>
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
    		        <a onclick="return confirm('Return without saving?')" href="/generator" class="pure-button" style="float:left;">&#8592; Back</a>
    		        <span style="margin-right: 100px" id="preview_text" class="results_1">Advanced options</span>
    		    </div>
    			
    			<br/> 
    			
	    		<div id="errors" class="errors"><?php 
	    	           if(isset($_SESSION['errors'])) {
	    	               foreach($_SESSION['errors'] as $error) {
	    	                   echo "<p>" . $error . "</p>";
	    	               }
	                       unset($_SESSION['errors']);
	                   }
				?></div>
	    	    
				<form id="form_data" action="advanced_handler" method="post" enctype="multipart/form-data" onsubmit="return checkAdvancedOptionsForm()">
        	        <div>
            	        <table id="formTable1">
                	        
                	        <tr>
                                <td><label for="proportions">Keep avatar proportions:</label></td>
                                <td><input type="checkbox" name="proportions" id="proportions" <?php if($proportions) echo 'checked'; ?>> 
                                    <div style="display: none;" class="tooltip">A simple tooltip</div>
                                    <span id="help1" style="color:#3973ac;border-bottom:1px dotted;">(What's this?)</span></td>
                            </tr>
                            
                            <tr>
                                <td><label for="flagOverrideCheck">Override flag:</label></td>
                                <td>
                                    <input type="checkbox" name="flagOverride" id="flagOverride" <?php if($flagOverride) echo 'checked'; ?>>
                                    <select id="flagOverrideData" name="flagOverrideData" disabled>
                                        <?php foreach(getFlagList() as $flag) { ?>
                                            <option value="<?=$flag?>"<?=$flag==$flagOverrideData?' selected':''?>></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td><label for="imageOverride">Override avatar image:</label></td>
                                <td>
                                    <input type="checkbox" name="imageOverride" id="imageOverride" <?php if($imageOverride) echo 'checked'; ?>>
                                    <div id="imageSelectorDivActive" style="display: inline-block">
                                        <input type="file" name="customImage" id="customImage" accept=".png,.jpg,.jpeg,.gif" disabled>
                                        <input type="hidden" id="submittedImage" name="submittedImage" value="<?=$imageOverrideFileName?>">
                                    </div>
                                    <div id="imageSelectorDivInactive" style="display: none">
                                        Submitted file: <?=$imageOverrideFileName?> &nbsp; <button type="button" onclick="resetImgForm()">Reset</button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td><label for="noAvatar">Don't use avatar:</label></td>
                                <td><input type="checkbox" name="noAvatar" id="noAvatar" <?php if($noAvatar) echo 'checked'; ?>></td>
                            </tr>
                	        
                        </table>
                    </div>
                    
                    <br/>
                    
                   <div style="height: 45px;">
                       <button class='pure-button pure-button-primary' id="submit" type="submit">Save</button>
                       <div id="loading" style="display: none" class="cssload-loader"></div>
                   </div>
                    
        	    </form>
    	    </div>
    	</div>
    	</div>
    	
    	<?php include 'footer.php'; ?>
    	<script src="js/scripts_advanced.js"></script>
	</body>
</html>