<!DOCTYPE html>
<html lang="ja">
  
  <head>
    <meta charset="UTF-8">
    <title>insert User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap styles -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <!-- Generic page styles -->
  <link rel="stylesheet" href="css/style.css">
  <!-- blueimp Gallery styles -->
  <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
  <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
  </head>
  <body>
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-fixed-top .navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.html">Evaluation Platform</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
            <!--
                <li><a href="upload.html">Upload</a></li>
                <li><a href="page2.html">page2</a></li>
                <li><a href="addUser.html">new Account</a></li>
            
            <li><a href="about.html">about</a></li>-->
            </ul>
        </div>
    </div>
</div>

<div class="container">
    <h1>Insert Account</h1>
    <h2 class="lead">Adding your account to the Database...</h2>


<?php
require 'lib/password.php';
require_once 'dsn.php';
$errorMessage="";
  if (empty($_POST["name"])) {
    $errorMessage = "ユーザIDが未入力です。";
  } else if (empty($_POST["password"])) {
    $errorMessage = "パスワードが未入力です。";
  }else{
$mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
if ($mysqli->connect_errno) {
    //die('接続失敗です。'.mysql_error());
    print('<p>データベースへの接続に失敗しました.</p>'.$mysqli->connect_error);
    exit();
}

//$db_selected = mysql_select_db($dsn['db'], $link);
$mysqli->select_db($dsn['db']);


$mysqli->set_charset('utf8');

$result = $mysqli->query('SELECT user_name , user_password FROM t_user');
if (!$result) {
    print('SELECTクエリーが失敗しました。'.$mysqli->error);
    exit();
}

$name = $_POST['name']; //入力されたユーザ名を受け取る
$password = $_POST['password']; //入力されたpasswordを受け取る
$hashpass = password_hash($password, PASSWORD_DEFAULT);//php 5.5以降


$sql = "INSERT INTO t_user (user_name, user_password) VALUES ('$name','$hashpass')";
$result_flag = $mysqli->query($sql);

if (!$result_flag) {
    print('すでに同じNAMEが登録されている可能性があります。<br><a href="addUser.html">戻る</a>'.$mysqli->error);
    exit();
}

print('<p>' . $name . 'ユーザーを登録しました。</p>');
mkdir('server/php/files/dataFiles/'.$name,0757);
chmod('server/php/files/dataFiles/'.$name,0757);

$mysqli->close();
}



if (!empty($errorMessage)) {
  echo "<p>{$errorMessage}</p>";
}
?>

  <a href="login.php">ログインページヘ</a>
  </div>
  <script src="js/jquery-1.12.4.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
  </body>
</html>
