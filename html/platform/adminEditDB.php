<?php
require_once 'dsn.php';


session_start();

// ログイン状態のチェック
if (!isset($_SESSION["USERID"])) {
  header("Location: logout.php");
  exit;
}




$dir='server/php/files/rawData/';
$f=array();
/*
if($dh=opendir($dir)){
  while(($file=readdir($dh))!==false){
    $files[]=$file;
  }
  closedir($dh);
}
*/
function json_safe_encode($data){
  return json_encode($data,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
}



function getFileList($dir) {
    $files = scandir($dir);
    $files = array_filter($files, function ($file) {
        return !in_array($file, array('.', '..'));
    });
 
    $list = array();
    foreach ($files as $file) {
        $fullpath = rtrim($dir, '/') . '/' . $file;
        if (is_file($fullpath)) {
            $list[] = $fullpath;
        }
        if (is_dir($fullpath)) {
            $list = array_merge($list, getFileList($fullpath));
        }
    }
 
    return $list;
}



$f=getFileList($dir);
$result=array();
$errorMessage=array();
$row=array();
$q=array_fill(0,count($f),'query');


//$q=$arr;


/*
for($i=0;$i<count($f);$i++){

  if(isset($_POST['create_'.$i])){
    if (empty($_POST['query_'.$i])) {
      $errorMessage[$i] = "クエリが未入力です.";
    }

    if(!empty($_POST['query_'.$i])){
      $mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
      if ($mysqli->connect_errno) {
        print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
        exit();
      }
      $mysqli->select_db($dsn['db']);
      // 入力値のサニタイズ
      $q= $mysqli->real_escape_string($_POST['query_'.$i]);
      // クエリの実行
      $query = "CREATE TABLE IF NOT EXISTS ".$q;
    // $query="create table t_test1(id int auto_increment)";
    if(!empty($q))$result[$i] = $mysqli->query($query);
      if(!$result[$i]) {
        print('クエリーが失敗しました。' . $mysqli->error);
        $mysqli->close();
        exit();
      }




      //echo $result;


      $mysqli->close();
    }


  }else{
    continue;
  }


}*/

$list=array();


$mainTable=$_POST['type'];
print('mainTable:'.$mainTable);
if(isset($_POST['select'])){
   $mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
      if ($mysqli->connect_errno) {
          print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
          exit();
        }
        $mysqli->select_db($dsn['db']);
        $mf=$_POST['type'];
        $query_reset="UPDATE t_filelist SET t_filelist_main=0";
        $r=$mysqli->query($query_reset);
        $query_mf='UPDATE t_filelist SET t_filelist_main=1 WHERE t_filelist_file="'.$mf.'"';
        $r=$mysqli->query($query_mf);
        if($r){
          print('main fileを'.$mf.'に設定しました.');
          print($query_mf);
        }else{
          print('main fileを設定できませんでした.'.$mysqli->error);
          $mysqli->close();
          exit();
        }
    $mysqli->close();

}

for($i=0;$i<count($f);$i++){
  
  if(isset($_POST['create_'.$i])){

    if (empty($_POST['query_'.$i])) {
      $errorMessage[$i] = "クエリが未入力です.";
    }

    print('radio:'.$_POST['radio']);
    if(!empty($_POST['query_'.$i])){
        $mysqli = new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
        if ($mysqli->connect_errno) {
          print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
          exit();
        }
        $mysqli->select_db($dsn['db']);
        // 入力値のサニタイズ
        $q[$i]= $mysqli->real_escape_string($_POST['query_'.$i]);
        // クエリの実行
        preg_match('/^(.+?)\(/',$q[$i],$m);//入力フォームからテーブル名を抽出

        $query_createTable = "CREATE TABLE IF NOT EXISTS ".$q[$i];
        $query_loadData ="LOAD DATA LOCAL INFILE '$f[$i]' INTO TABLE $m[1] FIELDS TERMINATED BY ',' IGNORE 1 LINES";
        // $query="create table t_test1(id int auto_increment)";
         
      if(!empty($q[$i])){
        
        $result[$i]=$mysqli->query($query_createTable);
        if(!$result[$i]) {
          print('テーブルが作成できませんでした.' . $mysqli->error);
          $mysqli->close();
          exit();
        }
        $result[$i]=$mysqli->query($query_loadData);
        if(!$result[$i]) {
          print('データをインポートすることができませんでした.' . $mysqli->error);
          $mysqli->close();
          exit();
        }else{
          print('<p>データを格納しました.</p>');
          $path=pathinfo($f[$i]);
          $file=$path['basename'];
          print($path['basename']);
          $query_insert="INSERT INTO t_filelist(t_filelist_table,t_filelist_file) VALUES('".$m[1]."','".$file."')";
          $result[$i]=$mysqli->query($query_insert);
          $query_select="SELECT t_filelist_file FROM t_filelist";
          if($result[$i]=$mysqli->query($query_select)){
            while($row=$result[$i]->fetch_assoc()){
              $list[]=$row['t_filelist_file'];
            }
          }


        }
      }

        $mysqli->close();
    }


     print($query_insert);



  }else{
    continue;
  }


}





?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>DB edit page (admin)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap styles -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <!-- Generic page styles -->
  <link rel="stylesheet" href="css/style.css">
  <!-- blueimp Gallery styles -->
  <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
  <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->

<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/black-tie/jquery-ui.css">

<link rel="stylesheet" href="js/grid-2.0.4/pqgrid.min.css">
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
            <a class="navbar-brand" href="admin.php">Evaluation Platform</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="adminFileUploader.php">File uploader(admin)</a></li>
                <li><a href="adminIndexUploader.php">Index uploader(admin)</a></li>
                <li><a href="logout.php">logout</a></li>
                <!--<li><a href="login.php">login</a></li>
                <li><a href="addUser.html">new Account</a></li>
                -->
            </ul>
             
        </div>
    </div>
</div>

<div class="container">
    <h1>DB Editer</h1>
    <h2 class="lead">User: <?php echo htmlspecialchars($_SESSION["USERID"], ENT_QUOTES); ?></h2>
  <!-- ユーザIDにHTMLタグが含まれても良いようにエスケープする -->

　<div id="mainTable">
  <h6>Please select main data.</h6>
</div>
  <div id="grid"></div>




   <div><a href="logout.php" type="button" class="btn btn-primary btn-lg" role="button">Logout</a></div>

</div>

<script src="js/jquery-1.12.4.min.js"></script>
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

<script src="js/grid-2.0.4/pqgrid.min.js"></script>

<script src="js/fixedTblHdrLftCol/jquery.fixedTblHdrLftCol-min.js"></script>


<script>
var d=<?php echo json_safe_encode($f)?>;
console.log(d);
console.log(<?php echo count($f)?>);
var flist=<?php echo json_safe_encode($list)?>;
var filename=new Array();

for(var i=0;i<d.length;i++){
  var m=d[i].match(/[^\/]*$/);
  filename[i]=m[0];
}
console.log(filename);
console.log(flist);




function csv2Array(filePath) {
  var csvData = new Array();
  var req = new XMLHttpRequest();
  req.open("GET", filePath, false); //true:非同期,false:同期
  req.send(null);

  var CR = String.fromCharCode(13);
  var LF = String.fromCharCode(10); //改行ｺｰﾄﾞ
  //var lines = req.responseText.split(LF);
  var lines = req.responseText.replace(/\r\n?/g,"\n").split(LF);
  console.log(lines);

  for (var i = 0; i < lines.length;++i) {
    var cells = lines[i].split(",");
    if( cells.length != 1 ) {
      csvData.push(cells);
    }
  }
  return csvData;
}

function jsonForColModel(csvArray){
  var temp='[';
  for(var i=0;i<csvArray[0].length;i++){
    temp+='{title:"'+csvArray[0][i]+'",width:80},';
  }
  var result=temp.substr(0,temp.length-1);
  result+=']';

return result;

}









$(function(){

/*
 var data=csv2Array(d[0]);
  var dat=csv2Array(d[1]);
 
    console.log(data);
    var obj = { width: 1200, height: 300, title: d[0],resizable:true,draggable:true };
    obj.colModel=(new Function("return "+jsonForColModel(data)))();
    console.log(obj.colModel);
        obj.dataModel = { data: data };
        $("#grid_array").pqGrid(obj);

    var ob = { width: 1200, height: 300, title: d[1],resizable:true,draggable:true };
    ob.colModel=(new Function("return "+jsonForColModel(dat)))();
    console.log(ob.colModel);
        ob.dataModel = {data:dat};
        $("#grid_array1").pqGrid(ob);
*/

var data=new Array();
var obj=new Array();
var grid_array=new Array();
var ct_obj=new Array();

//var q=<?php echo json_safe_encode($qre);?>;
//console.log(q);



         $('#mainTable').append('<form method="post">'
          +'<select name="type" class="c-select"><option selected>select main table</option></select>'
          +'<p><button type="submit" class="btn btn-default" name="select">submit</button></p>'
          +'</form>');

          for(var i=0;i<d.length;i++){
            $('.c-select').append('<option value="'+filename[i]+'">'+filename[i]+'</option>');
          }




    for(var i=0;i<d.length;i++){

            data[i]=csv2Array(d[i]);
            
            grid_array[i]='grid_array_'+i;
            obj[i] = { width: 1200, height: 300, title: d[i],resizable:true,draggable:true };
            obj[i].colModel=(new Function("return "+jsonForColModel(data[i])))();
            //console.log(obj[i].colModel);
            console.log(jsonForColModel(data[i]));
            //console.log(data[i].shift());
            obj[i].dataModel={data:data[i]};

/*
            ct_obj[i] = { width: 1200, height: 150, title: d[i],resizable:true,draggable:true };
            ct_obj[i].colModel=(new Function("return "+jsonForColModel(data[i])))();
            console.log(obj[i].colModel);
            ct_obj[i].dataModel = { data: data_type };
*/





            $('#grid').append('<h3>'+filename[i]+'</h3>');

          
                 $('#grid').append('<form class="form-inline" id="'+filename[i]+'"  action="" method="post">'
                +'<div class="form-group">'
                //+'<label class="sr-only" for="query">query</label>'
                +'CREATE TABLE IF NOT EXISTS <input type="text" class="form-control" id="query_'+i+'" size=100  name="query_'+i+'">;'
                +'</div>'
                +'<button type="submit" class="btn btn-default" name="create_'+i+'">Create table</button>'
                +'</form>');


            $('#grid').append('<div id="'+grid_array[i]+'"></div>');



            /*
            $('#grid').append('<h4>create table</h4>');
            $('#grid').append('<div id="ct_'+grid_array[i]+'"></div>');
             $('#ct_'+grid_array[i]).append('<table class="table table-bordered" id="t_'+i+'"><tr class="field"></tr><tr class="type"></tr></table>');
             $('#t_'+i+' .field').append('<td>field</td>');
             for(var k=0;k<data[i][0].length;k++){
             $('#t_'+i+' .field').append('<td>'+data[i][0][k]+'</td>');
           }
            $('#t_'+i+' .type').append('<td>type</td>');
             for(var k=0;k<data[i][0].length;k++){
             $('#t_'+i+' .type').append('<td>'+data[i][0][k]+'</td>');
           }

            $('#t_'+i+' tr').css('width',1200);
            $('#t_'+i+' tr td').css('width','100px');
           // $('#t_'+i).css('height',100);

            $('#t_'+i).fixedTblHdrLftCol({scroll:{
            height: '150px',
            width: '600px'
          }});
          */
          $('#'+grid_array[i]).pqGrid(obj[i]);
    }
console.log("grid_array:"+grid_array);


for(var i=0;i<flist.length;i++){
    $('#'+flist[i]).empty();
    $('#'+flist[i]).append('<fieldset disabled>'
          +'<div class="form-group">'
          +'CREATE TABLE IF NOT EXISTS <input type="text" class="form-control" id="query_'+i+'" size=100  name="query_'+i+'">;'
          +'</div>'
          +'<button type="submit" class="btn btn-default" name="create_'+i+'">Create table</button>'
          +'</fieldset>'
    );
 }






});






</script>




  </body>
</html>