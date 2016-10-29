<?php

    set_exception_handler("exceptionHandler");
    set_error_handler("errorHandler");
    
    define("CUSTOM_IMAGES_UPLOAD_DIR", dirname(__FILE__) . "/../userCustomImages");
    define("TEMP_DIR_GENERATOR", dirname(__FILE__) . "/../temp");
    
    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    function returnWithError($msg, $location) {
        returnWithErrors(array($msg), $location);
    }
	
	function returnWithErrors($array, $location) {
        $_SESSION['errors'] = $array;
        header("Location: $location");
        die();
    }
    
    function getDateStr() {
        return date_format(date_create(), 'Y-m-d H-i-s');
    }
    
    function exceptionHandler($ex) {
        $filePath = dirname(__FILE__) . "/../logs/error/EXC " . getDateStr();
        
        if(isset($_SESSION['username'])) {
            $filePath .= " (". $_SESSION['username'] . ")";   
        }
        
        $filePath .= ".txt";
        
        $text =  $ex->getMessage() . "\r\n\r\n"
                . "in " . $ex->getFile() . " at line " . $ex->getLine() . "\r\n\r\n"
                . $ex->getTraceAsString() . "\r\n";
                
        $text .= "\r\nSession ID: " . $_COOKIE["PHPSESSID"];        
                
        if(isset($_SESSION['form'])) {
            foreach($_SESSION['form'] as $key=>$val) {
                $text .= "\r\n$key: $val";
            }
        }
		
		if(isset($_SESSION['advanced'])) {
            foreach($_SESSION['advanced'] as $key=>$val) {
                $text .= "\r\n$key: $val";
            }
        }
        
        $text .= "\r\nfrom " . $_SERVER['REMOTE_ADDR'];
                
        file_put_contents($filePath, $text, FILE_APPEND | LOCK_EX);
        returnWithError("There has been an unexpected error, and an error log has been generated. Please report this issue if it persists.", "/generator");
    }
    
    function errorHandler($errno, $errstr, $errfile, $errline) {
        $filePath = dirname(__FILE__) . "/..//logs/error/ERR " . getDateStr();
        
        if(isset($_SESSION['username'])) {
            $filePath .= " (". $_SESSION['username'] . ")";
        }
        
        $filePath .= ".txt";
        
        $text =  $errstr . "\r\n\r\n"
                . "in " . $errfile . " at line " . $errline . "\r\n";
				
		$text .= "\r\nSession ID: " . $_COOKIE["PHPSESSID"];
        
        if(isset($_SESSION['form'])) {        
            foreach($_SESSION['form'] as $key=>$val) {
                $text .= "\r\n$key: $val";
            }
        }
		
		if(isset($_SESSION['advanced'])) {
            foreach($_SESSION['advanced'] as $key=>$val) {
                $text .= "\r\n$key: $val";
            }
        }
        
        $text .= "\r\nfrom " . $_SERVER['REMOTE_ADDR'];
		
		if($errline == 0 && $errfile == "Unknown") {
			$errors = array("There has been a problem reading your avatar. This is unusual and could be due to your avatar image containing errors.", "Please, try to upload your avatar in a different file type and wait a few minutes for the osu! avatar cache to refresh.");
		} else {
			$errors = array("There has been an unexpected error, and an error log has been generated. Please report this issue if it persists.");
		}
        
        file_put_contents($filePath, $text, FILE_APPEND | LOCK_EX);
        returnWithErrors($errors, "/generator");
                
    }

    function getFontList() {
        $res = [];
        $i = 0;
        foreach(scandir(dirname(__FILE__)."/../fonts/") as $filename) {
            if($i++ < 2) continue;
            $res[] = substr($filename, 0, -4);
        }
        return $res;
    }

    function getFlagList() {
        $res = [];
        $i = 0;
        foreach(scandir(dirname(__FILE__)."/../flags/") as $filename) {
            if($i++ < 2 || $filename == "a1.gif" || $filename == "ap.gif") continue;
            $res[] = substr($filename, 0, -4);
        }
        return $res;
    }
    
    function folderSize($dir) {
        $count_size = 0;
        
        foreach(scandir($dir) as $filename){
            
            if($filename == ".." || $filename == ".") continue;
            
            if(is_dir($dir."/".$filename)) {
                $count_size += folderSize($dir."/".$filename);    
            } else if(is_file($dir."/".$filename)) {
                $count_size += filesize($dir."/".$filename);
            }
            
        }
        
        return $count_size;
    }

?>