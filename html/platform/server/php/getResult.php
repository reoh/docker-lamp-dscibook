<?php

require_once '../../dsn.php';

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
    $query = "SELECT * FROM t_result WHERE t_result_update=(SELECT MAX(t_result_update) FROM t_result)" ;
    $result = $mysqli->query($query);
    if (!$result) {
      print('クエリーが失敗しました。' . $mysqli->error);
      $mysqli->close();
      exit();
    }

  

    

  $table='';
  $rows=array();

    while($row = $result->fetch_assoc()){
      $table.="<tr>";
      $table.="<td>{$row['t_result_id']}</td>";
      $table.="<td>{$row['t_result_user']}</td>";
      $table.="<td>{$row['t_result_data']}</td>";
      $table.="</tr>";

      $rows[]=$row;

    }
    $result->free();

   // データベースの切断
    $mysqli->close();

$col=array_keys($rows[0]);

$output=array();
$output[]=$col;
$outText="";


$th.='<tr>';


for($i=0;$i<count($col);$i++){
 if($rows[0][$col[$i]]!==NULL){
	$th.='<th>';
	$th.=str_replace("t_result_","",$col[$i]);
	$th.='</th>';
	$outText.=str_replace("t_result_","",$col[$i]);
	$outText.=",";
}


}
$outText=rtrim($outText,",");
$outText.="¥";
$th.='</tr>';
$td='';
foreach ($rows as $row) {
	$outText.="";
	$td.='<tr>';
	for($i=0;$i<count($col);$i++){
		if($row[$col[$i]]!==NULL){
		$td.='<td>'.$row[$col[$i]].'</td>';
		$outText.=$row[$col[$i]];
		$outText.=",";
		}
	}
	$outText=rtrim($outText,",");
	$outText.="¥";
	$td.='</tr>';
	$output[]=$row;

}
$outText=rtrim($outText,",");


$arr=explode("¥",$outText);
$tm=array_pop($arr);
$out=array();
for($i=0;$i<count($arr);$i++){
	array_push($out,explode(",",$arr[$i]));
}


/*
	//echo '<p>'.$row['t_result_update'].'</p>';
    //echo '<table id="resultTable" class="table table-bordered">';
    //echo '<table id="resultTable">';
    echo '<thead>';
  	echo $th;
  	echo '</thead>';
  	echo '<tbody>';
  	echo $td;
  	echo '</tbody>';
  	//echo '</table>';
 */


  	//echo "<pre>";
  	//print_r($out);
  	//echo json_safe_encode($output);
	//echo "</pre>";
   echo json_encode($out);

