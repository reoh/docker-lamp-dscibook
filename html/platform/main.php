<?php

session_start();

// ログイン状態のチェック
if (!isset($_SESSION["USERID"])) {
  header("Location: logout.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>user main page</title>
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
            <a class="navbar-brand" href="">Evaluation Platform</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="fileUploader.php">File uploader</a></li>
                <li><a href="indexUploader.php">Index uploader</a></li>
                <li><a href="list.php">Results</a></li>
                <li><a href="logout.php">Logout</a></li>
                <!--<li><a href="login.php">login</a></li>
                <li><a href="addUser.html">new Account</a></li>
                -->
            </ul>
             
        </div>
    </div>
</div>

<div class="container">
    <h1>Evaluation Platform</h1>
    <h2 class="lead">User: <?php echo htmlspecialchars($_SESSION["USERID"], ENT_QUOTES); ?></h2>
  <!-- ユーザIDにHTMLタグが含まれても良いようにエスケープする -->
              <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">匿名加工データを一括に評価するプラットフォームです.</h3>
        </div>
        <div class="panel-body">
            <h6>使い方</h6>
            <ol>
                <li><a href="fileUploader.php">File uploderページ</a>で加工ファイルを提出</li>
                <li><a href="indexUploader.php">Index uploderページ</a>で有用性指標&安全性指標スクリプトを提出</li>
                <li><a href="list.php">Resultsページ</a>に結果が表示される.</li>
            </ol>
        </div>
    </div>




   <div><a href="logout.php" type="button" class="btn btn-primary btn-lg" role="button">Logout</a></div>

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