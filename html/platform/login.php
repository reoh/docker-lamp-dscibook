<?php
require 'lib/password.php';
require_once 'dsn.php';
// セッション開始
session_start();

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
  // １．ユーザIDの入力チェック
  if (empty($_POST["userid"])) {
    $errorMessage = "ユーザIDが未入力です。";
  } else if (empty($_POST["password"])) {
    $errorMessage = "パスワードが未入力です。";
  } 

  // ２．ユーザIDとパスワードが入力されていたら認証する
  if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
    // mysqlへの接続
    $mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
    if ($mysqli->connect_errno) {
      print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
      exit();
    }

    // データベースの選択
    $mysqli->select_db($dsn['db']);

    // 入力値のサニタイズ
    $userid = $mysqli->real_escape_string($_POST["userid"]);

    // クエリの実行
    $query = "SELECT * FROM t_user WHERE t_user_name = '" . $userid . "'";
    $result = $mysqli->query($query);
    if (!$result) {
      print('クエリーが失敗しました。' . $mysqli->error);
      $mysqli->close();
      exit();
    }

    while ($row = $result->fetch_assoc()) {
      // パスワード(暗号化済み）の取り出し
      $db_hashed_pwd = $row['t_user_password'];
    }

    // データベースの切断
    $mysqli->close();

    // ３．画面から入力されたパスワードとデータベースから取得したパスワードのハッシュを比較します。
    //if ($_POST["password"] == $pw) {
    if (password_verify($_POST["password"], $db_hashed_pwd)) {
      // ４．認証成功なら、セッションIDを新規に発行する
      session_regenerate_id(true);
      $_SESSION["USERID"] = $_POST["userid"];

      if($_SESSION["USERID"]==='root') header("Location:admin.php");
      else header("Location: main.php");

      exit;
    }
    else {
      // 認証失敗
      $errorMessage = "ユーザIDあるいはパスワードに誤りがあります。";
    }
  } else {
    // 未入力なら何もしない
  }
}
?>
<!doctype html>
<html lang="ja">
  <head>
  <meta charset="UTF-8">
  <title>Login page</title>
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
                -->
                <li><a href="about.html">about</a></li>
                <li><a href="addUser.html">new Account</a></li>

            
            </ul>
        </div>
    </div>
</div>
<div class="container">
    <h1>Login page</h1>
    <h2 class="lead">Please login.</h2>  <!-- $_SERVER['PHP_SELF']はXSSの危険性があるので、actionは空にしておく -->
  <!--<form id="loginForm" name="loginForm" action="<?php // print($_SERVER['PHP_SELF']) ?>" method="POST">-->
 <!-- <form id="loginForm" name="loginForm" action="" method="POST">
  <fieldset>
  <legend>login form</legend>
  <div><?php echo $errorMessage ?></div>
  <label for="userid">User ID</label><input type="text" id="userid" name="userid" value="<?php echo htmlspecialchars($_POST['userid'], ENT_QUOTES); ?>">
  <label for="userid">User ID</label><input type="text" id="userid" name="userid" value="">
  <br>
  <label for="password">Password</label><input type="password" id="password" name="password" value="">
  <br>
  <input type="submit" id="login" name="login" value="Login">
  </fieldset>
  </form>-->

<form id="loginForm" name="loginForm" action="" method="post">
    <div class="form-group">
      <div><?php echo $errorMessage ?></div>
      <label>User ID</label>
      <input type="text" class="form-control" placeholder="User ID"  name="userid">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" class="form-control" placeholder="Password" name="password">
  </div>
  <button type="submit" class="btn btn-default" name="login">Login</button>
    </form>

  <a href="addUser.html">ユーザー情報登録ページへ</a>

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