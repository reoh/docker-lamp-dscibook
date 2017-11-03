<?php
ini_set("display_errors", On);
error_reporting(E_ALL);

/*
session_start();

if(!isset($_SESSION["USERID"])){
	header('Location: ../../logout.php');
	exit();
}

error_reporting(E_ALL|E_STRICT);
*/


require('index.php');
require_once '../../dsn.php';

$myOptions=new myOptions();


$upDirName='files/indexFiles'; // アップロード先のディレクトリ


$upDir='/'.$upDirName.'/';

$options=array(
 	'upload_dir' => $myOptions->uploadDir().$upDir,
 	'upload_url' => $myOptions->uploadUrl().$upDir,
 	'delete_type' =>'POST',
  'accept_file_types' => '/(\.|\/)(py|R)$/i',
  'mkdir_mode' =>0757
);


$upload_handler = new MyUploadHandlerForIndexHandler($options);














function db_col_insert($indexFiles){
      $mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
      if ($mysqli->connect_errno) {
        print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
        exit();
      }

    // データベースの選択
    $mysqli->select_db($dsn['db']);
    

    // クエリの実行
   for($i=0;$i<count($indexFiles);$i++){
    $query = "DESCRIBE t_result $indexFiles[$i]" ;
    $result = $mysqli->query($query);
    	if (!$result) {
      	print('クエリーが失敗しました。' . $mysqli->error);
      	$mysqli->close();
      	exit();
    	}
	}
    
 }

 ?>

 
