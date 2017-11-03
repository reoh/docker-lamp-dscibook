<?php

/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */


//const FILESTORE = true;

/*
 * FILESTORE=true : upload Directory = /files/userID/filename
 * FILESTORE=false: upload Directory = /files/userID_filename
 */

session_start();

if(!isset($_SESSION["USERID"])){
    header("Location: ../../logout.php");
    exit;
}

error_reporting(E_ALL | E_STRICT);
//error_reporting(E_STRICT);
require('UploadHandler.php');


class myOptions{

    function get_server_var($id) {
   		return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
  	}

 	function get_full_url() {
  		$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;

  		return  ($https ? 'https://' : 'http://').(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
  				(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
   				($https && $_SERVER['SERVER_PORT'] === 443 || $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
  				substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
 	}

 	function uploadDir(){
  		return dirname($this->get_server_var('SCRIPT_FILENAME'));
 	}

 	function uploadUrl(){
  		return $this->get_full_url();
 	}

}




class MyUploadHandlerForFileHandler extends UploadHandler{




}


class MyUploadHandlerForIndexHandler extends UploadHandler{


   protected function trim_file_name($file_path, $name, $size, $type, $error,
            $index, $content_range) {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $name = trim($this->basename(stripslashes($name)), ".\x00..\x20");
        $name = $_SESSION["USERID"].'_'.$name;
        // Use a timestamp for empty filenames:
        if (!$name) {
            $name = str_replace('.', '-', microtime(true));
        }
        return $name;
    }




}


class Test{
  public function __construct(){
    print('<p>Hello</p>');
  }
}

class MyUploadHandlerForAdminFileHandler extends UploadHandler{

}

class MyUploadHandlerForAdminIndexHandler extends UploadHandler{

}







