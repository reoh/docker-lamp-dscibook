<?php
require_once 'dsn.php';
//require 'server/php/calculate.php';

session_start();

// ログイン状態のチェック
if (!isset($_SESSION["USERID"])) {
  header("Location: logout.php");
  exit;
}


function json_safe_encode($data){
  return json_encode($data,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
}

  $mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
    if ($mysqli->connect_errno) {
      print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
      exit();
    }

    // データベースの選択
    $mysqli->select_db($dsn['db']);

    // クエリの実行
    $query = "SELECT t_user_id,t_user_name FROM t_user" ;
    $result = $mysqli->query($query);
    if (!$result) {
      print('クエリーが失敗しました。' . $mysqli->error);
      $mysqli->close();
      exit();
    }


  $table='';

    while($row = $result->fetch_assoc()){
      $table.="<tr>";
      $table.="<td>{$row['t_user_id']}</td>";
      $table.="<td>{$row['t_user_name']}</td>";
      $table.="</tr>";

     // echo $row['t_user_name']."/";
      $nameList[]=$row['t_user_name'];

    }

    //print_r($nameList);



   // データベースの切断
    $mysqli->close();




//exec('/usr/bin/php server/php/calculate.php > /dev/null &')



?>



<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>User list</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap styles -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.jqueryui.min.css">

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
            <a class="navbar-brand" href="main.php">Evaluation Platform</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="fileUploader.php">File uploader</a></li>
                <li><a href="indexUploader.php">Index uploader</a></li>
                <li><a href="logout.php">Logout</a></li>
                <!--<li><a href="login.php">login</a></li>
                <li><a href="addUser.html">new Account</a></li>
                -->
            </ul>
        </div>
    </div>
</div>

<div class="container">
    <h1>Results</h1>
   <h2 class="lead"> User: <?php echo htmlspecialchars($_SESSION["USERID"], ENT_QUOTES); ?></h2>
   <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Notes</h3>
        </div>
        <div class="panel-body">
            <ul>
            <li>Safety:安全性指標の中で最も数値が高い(危険)値</li>
            <li><a href=<?php echo $rf[0]; ?> >被加工データ</a>に対する加工データの評価値</li>
            <li>有用性指標U:数値が低いほど良い(U≧0)</li>
            <li>安全性(再識別)指標S:数値が低いほど安全(0≦S≦1)</li>
            </ul>
        </div>
    </div>


  <div id="data">
    <h3>Performance</h3>

    <div id="getCheck">
    </div>

    <div id="result_table">
      <table id="resultTable" class="table table-bordered">
    </table>
  </div>

</div>


</div>

<script src="js/jquery-1.12.4.min.js"></script>

<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>

<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
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

<script id="script" src="js/main_list.js">
  
</script>

<script>

  var nameList=<?php echo json_safe_encode($nameList);?>;
  var indexes=<?php echo json_safe_encode($indexFile);?>;
  var basename_index=<?php echo json_safe_encode($basename_index);?>;
  var scriptResult=<?php echo json_safe_encode($scriptResult);?>;
  console.log(nameList);


   $(function(){


  // console.log($('table').text());




   });
</script>

  </body>
</html>

