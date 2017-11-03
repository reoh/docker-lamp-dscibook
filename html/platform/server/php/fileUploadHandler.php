<?php

/*
session_start();

if(!isset($_SESSION["USERID"])){
	header('Location: ../../logout.php');
	exit();
}

error_reporting(E_ALL|E_STRICT);
*/

require('index.php');

$myOptions=new myOptions();

$upDirName='files/dataFiles/'.$_SESSION["USERID"]; // アップロード先のディレクトリ


$upDir='/'.$upDirName.'/';

$options=array(
 	'upload_dir' => $myOptions->uploadDir().$upDir,
 	'upload_url' => $myOptions->uploadUrl().$upDir,
 	'delete_type'=>'POST',
 	'accept_file_types' => '/(\.|\/)(csv)$/i'  //csvファイルのみアップロード可能
);



$upload_handler = new MyUploadHandlerForFileHandler($options);


