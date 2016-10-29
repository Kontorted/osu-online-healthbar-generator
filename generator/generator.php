<?php
    
    include "etc/utilities.php";
    
    session_start();

    if($_SERVER['REQUEST_METHOD'] != "POST") {
        header("Location: /generator");
        die();
    }
    
    extract($_POST);
	
	if(!isset($username) || !isset($text) || !isset($font) || !isset($fontsize) 
    	|| !isset($color1) || !isset($color2) || !isset($color3) || !isset($color4) || !isset($color5)) {
    		returnWithError("Please provide all required fields", "/generator");
    }
    
    //Convert cbeckboxes to booleans
    $useFlag = isset($useFlag);
    $oldFlags = isset($oldFlags);
    $square = isset($shape) && $shape == 'shape_square';
    
    //Store form data
    $_SESSION['form'] = array(
        "username" => $username,
        "text" => $text,
        "useFlag" => $useFlag,
        "oldFlags" => $oldFlags,
        "font" => $font,
        "fontsize" => $fontsize,
        "square" => $square,
        "colorTheme" => $colorTheme,
        "color1" => $color1,
        "color2" => $color2,
        "color3" => $color3,
        "color4" => $color4,
		"color5" => $color5
    );
    
	//Store username from the previous request in case it has changed and we have to download avatar and flag again
    if(isset($_SESSION['username'])) {
        $_SESSION['previous_username'] = $_SESSION['username'];
    } else {
        $_SESSION['previous_username'] = "";
    }
    
    $_SESSION['username'] = $username;
    
    //Extract advanced options, which have been previously validated
    if(!isset($_SESSION['advanced'])) {
        $proportions = false;
        $flagOverride = false;
        $flagOverrideData = "";
        $imageOverride = false;
        $noAvatar = false;
    } else {
        extract($_SESSION['advanced']);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////// Check for errors in input data ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //Check that username is set
    if($username == "") {
        returnWithError("Please, fill in your username", "/generator");
    }
    
    $username = trim($username);
    
    //Check that font size is a number between 1 and 100
    if(!((string)(int)$fontsize == $fontsize)) {
        returnWithError("Font size must be an integer", "/generator");
    }
    
    $fontsize = (int) $fontsize;
    
    if($fontsize < 1 || $fontsize > 100) {
        returnWithError("Font size must be between 1 and 100", "/generator");
    }
    
	//Check that hex color codes are valid
    $regex_text = "/^#[a-fA-F0-9]{6}$/";
    
    if(!preg_match($regex_text, $color1)) {
        returnWithError($color1 . " is not a valid color", "/generator");
    }
    
    if(!preg_match($regex_text, $color2)) {
        returnWithError($color2 . " is not a valid color", "/generator");
    }
    
    if(!preg_match($regex_text, $color3)) {
        returnWithError($color3 . " is not a valid color", "/generator");
    }
    
    if(!preg_match($regex_text, $color4)) {
        returnWithError($color4 . " is not a valid color", "/generator");
    }
	
	if(!preg_match($regex_text, $color5)) {
        returnWithError($color5 . " is not a valid color", "/generator");
    }
    
    if(!isset($font)) {
        returnWithError("Please, select a font", "/generator");
    }
    
    if(array_search($font, getFontList()) === false) {
        returnWithError("Font not found", "/generator");
    }
    
    //Convert hex colors to RGB arrays
    $bg_color = sscanf($color1, "#%02x%02x%02x");
    $hb_color = sscanf($color2, "#%02x%02x%02x");
    $font_color = sscanf($color3, "#%02x%02x%02x");
    $border_color = sscanf($color4, "#%02x%02x%02x");
	$bar_border_color = sscanf($color5, "#%02x%02x%02x");
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////// Obtain data from osu! API /////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //Issue the request
    $api_key = YOUR_API_KEY_HERE;
    
    $url = "https://osu.ppy.sh/api/get_user?m=2&type=string&k=" . $api_key . "&u=" . urlencode($username);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
    $query = curl_exec($curl);
    
    $json = json_decode($query, true);
    
    //Check that the username exists
    if(!isset($json[0])) {
        returnWithError("User not found!", "/generator");
    }
    
    //If no text to print has been requested, copy the username.
    $textToPrint = ($text == "") ? $json[0]['username'] : $text;
    $userID = $json[0]['user_id'];
    $countryCode = $json[0]['country'];
    
    //Override if custom country code is set
    if($flagOverride) {
        $countryCode = strtoupper($flagOverrideData);
    }
    
    //Store the requested country from the previous request, because we have to download the flag again if they are different
    if(isset($_SESSION['countrycode'])) {
        $_SESSION['previous_countrycode'] = $_SESSION['countrycode'];
    } else {
        $_SESSION['previous_countrycode'] = "";
    }
    
    $_SESSION['countrycode'] = $countryCode;
    
    if($countryCode == "" && $useFlag) {
        $_SESSION['errors'] = array("osu! api returned empty country code for your username. Omitting flag.");
        $useFlag = false;
    }
    
    //Check that the user has an avatar
    $url = "http://s.ppy.sh/a/$userID";
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    
    if($code == 403 || $code == 404) {
        $hasAvatar = false;
    } else {
        $hasAvatar = true;
    }
    
    //Override if the 'dont use avatar' option is selected
    $hasAvatar = $hasAvatar && !$noAvatar;
    
    //Check that the flag exists in case the user has requested for the new flag designs.
    if($useFlag && !$oldFlags) {
        $url = "http://new.ppy.sh/images/flags/$countryCode.png";
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($code == 404) {
            $_SESSION['errors'] = array("Couldn't find the new flag style for your country. Using the old one instead.");
            $oldFlags = true;
        }
    }
    
    curl_close($curl);
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////// Download and store images /////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
    //Download and resize user avatar, if they have one
    if($hasAvatar) {
        
        if($imageOverride) {
            $avatarPath = dirname(__FILE__) . "/userCustomImages/" . session_id() . "_custom";
        } else {
           $avatarPath = dirname(__FILE__) . "/temp/" . session_id() . "_avatar";
           
            if(!file_exists($avatarPath) || $_SESSION['previous_username'] != $_SESSION['username']) {
                $avatarURL = "http://s.ppy.sh/a/$userID";
                downloadImage($avatarURL, $avatarPath);
            } 
        }
        
        $avatarData = getimagesize($avatarPath);
        $avatarImg = readImage($avatarPath, $avatarData[2]);
        
        //If the user has requested to maintain avatar proportions, do it instead of resizing to 128x128
        if($proportions) {
            $p1 = $avatarData[0] / 128;
            $p2 = $avatarData[1] / 128;
            $factor = max($p1, $p2);
            $newAvatarWidth = $avatarData[0] / $factor;
            $newAvatarHeight = $avatarData[1] / $factor;
        } else {
            $newAvatarWidth = 128;
            $newAvatarHeight = 128;
        }
        
        $avatarImg = resize($avatarImg, $newAvatarWidth, $newAvatarHeight, $avatarData[0], $avatarData[1], false, true);
    
        //If the avatar must maintain proportions and it's not a square, create an empty 128x128 image and center the avatar on it
        if($proportions && !$square) {
            $aux = imagecreatetruecolor(128, 128);
            imagecopyresampled($aux, $avatarImg, (128-$newAvatarWidth)/2, (128-$newAvatarHeight)/2, 0, 0, $newAvatarWidth, $newAvatarHeight, $newAvatarWidth, $newAvatarHeight);
            $avatarImg = $aux;
        }
        
    }
    
    //Download and store flag if neccesary
    if($useFlag) {
        
        if($oldFlags) {
            $flagPath = dirname(__FILE__) . "/flags/" . strtolower($countryCode) . ".gif"; 
        } else {
            $flagPath = dirname(__FILE__) . "/temp/" . session_id() . "_flag";
            if(!file_exists($flagPath) || $_SESSION['previous_countrycode'] != $_SESSION['countrycode']) {
                $flagURL = "http://new.ppy.sh/images/flags/$countryCode.png";
                downloadImage($flagURL, $flagPath);
            }
        }
        
        $flagData = getimagesize($flagPath);
        $flagImg = readImage($flagPath, $flagData[2]);
        $flagImg = resize($flagImg, 32, 22, $flagData[0], $flagData[1]);
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////// Image generation process /////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //Fetch and recolor templates
    $templateBGpath = dirname(__FILE__) . "/templates/1_bg.png";
    $templateBGdata = getimagesize($templateBGpath);
    $templateBG = readImage($templateBGpath, $templateBGdata[2]);
    
    $templateColorpath = dirname(__FILE__) . "/templates/1_color.png";
    $templateColordata = getimagesize($templateColorpath);
    $templateColor = readImage($templateColorpath, $templateColordata[2]);
    
    colorize($templateBG, $bg_color, $templateBGdata[0], $templateBGdata[1], $bar_border_color);
    colorize($templateColor, $hb_color, $templateColordata[0], $templateColordata[1]);
    
    if($hasAvatar) {
        $avatarBorderPath = dirname(__FILE__) . "/templates/1_" . ($square ? "square" : "circle") . ".png";
        $avatarBorderData = getimagesize($avatarBorderPath);
        $avatarBorder = readImage($avatarBorderPath, $avatarBorderData[2]);
        
        colorize($avatarBorder, $border_color, $avatarBorderData[0], $avatarBorderData[1]);
        
        //Round avatar border
        createRoundedCorners($avatarImg, $square ? 7 : 64, 128, 128);
    }
    
    //Disable alpha blending to be able to place avatar and border on top of it
    imagealphablending($templateBG, true);
    
    //Print the text
    $textXpos = 137;
    if($hasAvatar) $textXpos += 18;
    if($useFlag) $textXpos += 40;
    imagettftext($templateBG, (float) $fontsize, 0, $textXpos, 42, getColor($templateBG, $font_color), dirname(__FILE__). "/fonts/$font.ttf", html_entity_decode($textToPrint));
    
    //Place flag if neccesary
    if($useFlag) {
        $flagXpos = $hasAvatar ? 157 : 137;
        imagecopyresampled($templateBG, $flagImg, $flagXpos, 20, 0, 0, 32, 22, 32, 22);
    }
    
    //Place avatar and avatar border
    if($hasAvatar) {
        if($proportions && $square) {
            $relation = $newAvatarHeight/$newAvatarWidth;
            
            if($relation >= 1) {
                $newBorderWidth = 135 / $relation;
                $newBorderHeight = 135;
                $extraHor = floor(7 - (7 / $relation));
            } else {
                $newBorderWidth = 135;
                $newBorderHeight = 135 * $relation;
                $extraHor = 0;
            }
            
            $avatarBorder = resize($avatarBorder, $newBorderWidth, $newBorderHeight, 135, 135);
            
            imagecopyresampled($templateBG, $avatarImg, 19+128-$newAvatarWidth+$extraHor, 16+(128-$newAvatarHeight)/2, 0, 0, $newAvatarWidth, $newAvatarHeight, $newAvatarWidth, $newAvatarHeight);
            imagecopyresampled($templateBG, $avatarBorder, 17+135-$newBorderWidth+$extraHor, 14+(135-$newBorderHeight)/2, 0, 0, $newBorderWidth, $newBorderHeight, $newBorderWidth, $newBorderHeight);
        } else {
            imagecopyresampled($templateBG, $avatarImg, 19, 16, 0, 0, 128, 128, 128, 128);
            imagecopyresampled($templateBG, $avatarBorder, 17, 14, 0, 0, 135, 135, 135, 135);
        }
    }
    
    
    //Generate preview image
    $preview = resize($templateBG, 1354, 160, 1354, 160, true, true);
    imagecopyresampled($preview, $templateColor, 152, 68, 143, 36, 780, 24, 780, 24);
    
    //Generate low-res images
    $templateBGsmall = resize($templateBG, $templateBGdata[0]/2, $templateBGdata[1]/2, $templateBGdata[0], $templateBGdata[1]);
    $templateColorsmall = resize($templateColor, $templateColordata[0]/2, $templateColordata[1]/2, $templateColordata[0], $templateColordata[1]);

    //Store on temporary folder
    imagepng($preview, dirname(__FILE__) . "/temp/" . session_id() . "_0.png");
    imagepng($templateBG, dirname(__FILE__) . "/temp/" . session_id() . "_1.png");
    imagepng($templateColor, dirname(__FILE__) . "/temp/" . session_id() . "_2.png");
    imagepng($templateBGsmall, dirname(__FILE__) . "/temp/" . session_id() . "_3.png");
    imagepng($templateColorsmall, dirname(__FILE__) . "/temp/" . session_id() . "_4.png");
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////// Logging /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
	
	$logPath = dirname(__FILE__) . "/logs/success/" . strtolower($username) . ".txt";
	$logText = date_format(date_create(), 'Y-m-d H:i:s') . "\r\n\r\n";
	
	foreach($_SESSION['form'] as $key=>$val) {
		$logText .= "$key: $val\r\n";
	}
	
	if(isset($_SESSION['advanced'])) {
		foreach($_SESSION['advanced'] as $key=>$val) {
			$logText .= "\r\n$key: $val";
		}
	}
	
	$logText .= "\r\n=========================================================\r\n";
	
	file_put_contents($logPath, $logText, FILE_APPEND | LOCK_EX);
    
    header("Location: preview");
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    function createRoundedCorners($img, $radius, $width, $height) {
        
        imagealphablending($img, false);
        
        for($i = 0; $i <= $radius; $i++) {
            for($j = 0; $j <= $radius; $j++) {
                if(sqrt(pow($i-$radius,2) + pow($j-$radius,2)) > $radius) {
                    imagesetpixel($img, $i, $j, imagecolorallocatealpha($img,0,0,0,127));
                }
            }
        }
        
        for($i = $width - 1; $i >= $width - $radius; $i--) {
            for($j = 0; $j <= $radius; $j++) {
                if(sqrt(pow($i-$width+$radius,2) + pow($j-$radius,2)) > $radius) {
                    imagesetpixel($img, $i, $j, imagecolorallocatealpha($img,0,0,0,127));
                }
            }
        }
        
        for($i = 0; $i <= $radius; $i++) {
            for($j = $height - 1; $j >= $height - $radius; $j--) {
                if(sqrt(pow($i-$radius,2) + pow($j-$height+$radius,2)) > $radius) {
                    imagesetpixel($img, $i, $j, imagecolorallocatealpha($img,0,0,0,127));
                }
            }
        }
        
        for($i = $width - 1; $i >= $width - $radius; $i--) {
            for($j = $height - 1; $j >= $height - $radius; $j--) {
                if(sqrt(pow($i-$width+$radius,2) + pow($j-$height+$radius,2)) > $radius) {
                    imagesetpixel($img, $i, $j, imagecolorallocatealpha($img,0,0,0,127));
                }
            }
        }    
        
        imagealphablending($img, true);
    }
    
    function colorize($img, $newColor, $width, $height, $healthbarexterior=false) {
        
        $DARKENING_FACTOR = 1.6;
        $newColorDarker = array((int) $newColor[0] / $DARKENING_FACTOR, (int) $newColor[1] / $DARKENING_FACTOR, (int) $newColor[2] / $DARKENING_FACTOR);
        
        for($i = 0; $i < $width; $i++) {
            for($j = 0; $j < $height; $j++) {
                
                $color = imagecolorsforindex($img, imagecolorat($img, $i, $j));
                
				//Key colors are R=200 for darker color and R=100 for the requested color, but we need some error margin for the avatar border
                if($color['red'] >= 190 && $color['red'] <= 210) {
                    imagesetpixel($img, $i, $j, imagecolorallocatealpha($img, $newColor[0], $newColor[1], $newColor[2], $color['alpha']));
                } else if($color['red'] >= 90 && $color['red'] <= 110) {
                    imagesetpixel($img, $i, $j, imagecolorallocatealpha($img, $newColorDarker[0], $newColorDarker[1], $newColorDarker[2], $color['alpha']));
                } else if($healthbarexterior !== FALSE && $color['red'] == 255 && $color['green'] == 255 && $color['blue'] == 255) {
					imagesetpixel($img, $i, $j, imagecolorallocatealpha($img, $healthbarexterior[0], $healthbarexterior[1], $healthbarexterior[2], $color['alpha']));
				}
                
            }
        }
        
    }
    
    function getColor($image, $rgb, $alpha=0) {
        return imagecolorallocatealpha($image, $rgb[0], $rgb[1], $rgb[2], $alpha);
    }
    
    function resize($img, $width, $height, $oldwidth, $oldheight, $transparentBG = true, $alphaBlending = false) {
        $new = imagecreatetruecolor($width, $height);
        if($transparentBG) {
            $color = getColor($new, [0,0,0], 127);
        } else {
            $color = getColor($new, [255,255,255], 0);
        }
        imagesavealpha($new, true);
        imagealphablending($new, $alphaBlending);
        imagefill($new, 0, 0, $color);
        imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $oldwidth, $oldheight);
        return $new;
    }
    
    function readImage($path, $type) {
        switch($type) {
            case IMAGETYPE_GIF:
                $img = imagecreatefromgif($path);
                break;
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $img = imagecreatefrompng($path);
                break;
            case IMAGETYPE_WBMP:
                $img = imagecreatefromwbmp($path);
                break;
            case IMAGETYPE_XBM:
                $img = imagecreatefromxpm($path);
                break;
            default:
                returnWithErrors(array("There seems to be a problem reading your avatar.","Please, check that the image is not corrupt and that it has a suitable image type, such as png, jpg or gif."), "/generator");
        }
        
        imagesavealpha($img, true);
        imagealphablending($img, false);
        return $img;
    }
    
    function downloadImage($url, $path) {
        file_put_contents($path, file_get_contents($url));
    }
?>