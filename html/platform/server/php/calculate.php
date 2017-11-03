<?php
error_reporting(E_ALL);
ini_set("max_execution_time",1800);
ini_set("display_errors", DEBUG);


require '../../dsn.php';





$f=getFileList('files/dataFiles/');
$rawf=getFileList('files/rawData/');
$indexFile=getFileList('files/indexFiles/');

$path=array();
$basename_index=array();
$db_column=array();

for($i=0;$i<count($indexFile);$i++){
	$path[$i]=pathinfo($indexFile[$i]);
	$basename_index[$i]=$path[$i]['basename'];
    $db_column[$i]=str_replace(array(".","-"),"_",$basename_index[$i]);
}



$p=array();
for($i=0;$i<count($f);$i++){
    $p[$i]=explode("/",$f[$i]);
}






/*-------------------------指標スクリプトを実行-----------------------------------------*/
$scriptResult=array();
//exec("/home/home2014/reoh/.pyenv/shims/python files/indexFiles/reoh_sma_short.py files/dataFiles/reoh/changed_transaction_anony.csv files/rawData/changed_transaction.csv 2>&1",$scriptResult);
//$c=array();
//$tmp=array();


for($i=0;$i<count($f);$i++){
    $tmp=array("user","data");
	for($j=0;$j<count($indexFile);$j++){
        $extension=getExtension($indexFile[$j]);
        if($extension==="py"){
		//exec("Rscript --vanilla --slave $indexFile[$j] $f[$i]",$scriptResult[$i][$j]);
        $exe="/home/home2014/reoh/.pyenv/shims/python $indexFile[$j] $f[$i] $rawf[0] &";
       // $exe="python $indexFile[$j] files/dataFiles/ito/changed_transaction_anony.csv files/rawData/changed_transaction.csv";
       }elseif ($extension==="R") {
           $exe="Rscript --vanilla --slave $indexFile[$j] $f[$i] $rawf[0] &";
       }

        exec($exe,$scriptResult[$i]);
        $tmp[]=$db_column[$j];
        
        



       

        //var_dump($exe);
        //var_dump($scriptResult);
		//print("Rscript --vanilla --slave $indexFile[$j] $f[$i] : ".$scriptResult[$i][$j]."<br>");
		//print_r($scriptResult[$i][$j]);
        //print("<br>");
	}
    $fi=array_slice($p[$i],-2);
    array_unshift($scriptResult[$i],$fi[1]);
    array_unshift($scriptResult[$i],$fi[0]);
    $c[$i]=array_combine($tmp, $scriptResult[$i]);
    

}
//$c=array_combine($tmp, $scriptResult);




/*--------------------------------------------------------------------------------*/



 if(isset($_GET["button"])&&$_GET["button"]==="indexUpload"){
        /*
        echo $_GET["button"]."!!!";
        echo '<pre>';
        print_r($c);
        echo '</pre>';
        */


        //db_insert_from_indexUploader($db_column,$c);
        //関数化するとなぜか動かない-----------------------------------------------
        $mysqli= new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
        if ($mysqli->connect_errno) {
            print('<p>データベースへの接続に失敗しました.</p>'.$mysqli->connect_error);
            exit();
        }
        $mysqli->select_db($dsn['db']);
        $mysqli->set_charset('utf8');
        for($i=0;$i<count($db_column);$i++){
            $q_colCheck[$i]="DESCRIBE t_result $db_column[$i]";
            $result1[$i]=$mysqli->query($q_colCheck[$i]);

            
            if(strpos($db_column[$i],'U'))$q_alter[$i]="ALTER TABLE t_result ADD t_result_$db_column[$i] FLOAT AFTER t_result_data";
            else if(strpos($db_column[$i],'S'))$q_alter[$i]="ALTER TABLE t_result ADD t_result_$db_column[$i] FLOAT";

            //$q_alter[$i]="ALTER TABLE t_result ADD t_result_$db_column[$i] FLOAT AFTER t_result_data";
            $result2[$i]=$mysqli->query($q_alter[$i]);
            if(!$result2[$i]){
                   // echo "error!!!!!";
            }
        }
        for($i=0;$i<count($c);$i++){
            $utility=0;
            $safety=0;
                $keys[$i]=array_keys($c[$i]);
                $values[$i]=array_values($c[$i]);
                    for($j=0;$j<count($keys[$i]);$j++){
                //$col[$j]=$keys[$i][$j];
               if(strpos($keys[$i][$j],'U')){
                    echo $keys[$i][$j].":".$values[$i][$j]."//";
                    $utility+=$values[$i][$j];
                }else if(strpos($keys[$i][$j],'S')){
                    if($safety<$values[$i][$j])$safety=$values[$i][$j];
                    echo $keys[$i][$j].":".$values[$i][$j]."//";

                }
                
            }



                       $q_insert[$i]="INSERT INTO t_result(t_result_".implode(",t_result_",$keys[$i]).",t_result_safety) VALUES ('".implode("','",$values[$i])."',".$safety.")";


              // $q_insert[$i]="INSERT INTO t_result(t_result_".implode(",t_result_",$keys[$i]).") VALUES ('".implode("','",$values[$i])."')";
                $result3[$i]=$mysqli->query($q_insert[$i]);
        }







        $mysqli->close();
        //-------------------------------------------------------------------


    }else{
        /*
        echo $_GET["button"]."!!!";
        echo '<pre>';
        print_r($c);
        echo '</pre>';
        */

      //db_insert_from_fileUploader($c);
      //
      //関数化するとなぜか動かない----------------------------------------------
      $mysqli= new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
        if ($mysqli->connect_errno) {
            print('<p>データベースへの接続に失敗しました.</p>'.$mysqli->connect_error);
            exit();
        }
        $mysqli->select_db($dsn['db']);
        $mysqli->set_charset('utf8');

        for($i=0;$i<count($c);$i++){
            $utility=0;
            $safety=0;
                $keys[$i]=array_keys($c[$i]);
                $values[$i]=array_values($c[$i]);

                    for($j=0;$j<count($keys[$i]);$j++){
                //$col[$j]=$keys[$i][$j];
               if(strpos($keys[$i][$j],'U')){
                   // echo $keys[$i][$j].":".$values[$i][$j]."//";
                    $utility+=$values[$i][$j];
                }else if(strpos($keys[$i][$j],'S')){
                    if($safety<$values[$i][$j])$safety=$values[$i][$j];
                   //echo $keys[$i][$j].":".$values[$i][$j]."//";

                }
                
            }


                $q_insert[$i]="INSERT INTO t_result(t_result_".implode(",t_result_",$keys[$i]).",t_result_safety) VALUES ('".implode("','",$values[$i])."',".$safety.")";
                $result3[$i]=$mysqli->query($q_insert[$i]);

            
               //print_r($utility);
             
               

        }

        /*$q_u_rank_insert=
            "SELECT t_result_data, total_score, (select count(DISTINCT total_score) FROM total_scores b WHERE a.total_score < b.total_score) + 1 rank 
        FROM t_result a ORDER BY rank ASC WHERE t_result_update=(SELECT MAX(t_result_update) FROM t_result)";*/
       
        



       



   /*     $q_u_rank="UPDATE  (SELECT * FROM t_result WHERE t_result_update=(SELECT MAX(t_result_update) FROM t_result)) JOIN (SELECT p.`t_result_id`,
                    IF(@lastPoint <> p.`t_result_utility`,
                       @curRank := @curRank+1,
                       @curRank)  AS `t_result_rank_u`,
                    IF(@lastPoint = p.`t_result_utility`,
                       @curRank := @curRank+1,
                       @curRank),
                    @lastPoint := p.`t_result_utility`
          FROM     (SELECT * FROM t_result WHERE t_result_update=(SELECT MAX(t_result_update) FROM t_result)) p
          JOIN      (SELECT @curRank := 0, @lastPoint := 0) r
          ORDER BY  p.`t_result_utility` ASC
         ) ranks ON (ranks.`t_result_id` = t_result.`t_result_id`)
SET      t_result.`t_result_rank_u` = ranks.`t_result_rank_u`";
$result4=$mysqli->query($q_u_rank);

 $q_s_rank="UPDATE  t_result JOIN (SELECT t.`t_result_id`,
                    IF(@lastPoint <> t.`t_result_safety`,
                       @curRank := @curRank+1,
                       @curRank)  AS `t_result_rank_s`,
                    IF(@lastPoint = t.`t_result_safety`,
                       @curRank := @curRank+1,
                       @curRank),
                    @lastPoint := t.`t_result_safety`
          FROM     (SELECT * FROM t_result WHERE t_result_update=(SELECT MAX(t_result_update) FROM t_result)) t
          JOIN      (SELECT @curRank := 0, @lastPoint := -1) r
          ORDER BY  t.`t_result_safety` ASC
         ) ranks2 ON (ranks2.`t_result_id` = t_result.`t_result_id`)
SET      t_result.`t_result_rank_s` = ranks2.`t_result_rank_s`";

$q_rank="UPDATE t_result SET t_result_rank=t_result_rank_u+t_result_rank_s WHERE t_result_update=(SELECT MAX(t_result_update) FROM t_result) ";*/

//$result5=$mysqli->query($q_s_rank);
//$result6=$mysqli->query($q_rank);


        $mysqli->close();
        //--------------------------------------------------------------------

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

function json_safe_encode($data){
  return json_encode($data,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
}



function getf($a,$start){
    $length=-$start;
    $r=array();
    $r=array_slice($a,$start);
    $r1="";
    for($i=0;$i<=count($length);$i++){
    $r1.=$r[$i]."/";
    }
    $r2=substr($r1,0,-1);
    return $r2;
}



    function db_insert_from_fileUploader($a){
             $mysqli= new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
        if ($mysqli->connect_errno) {
            print('<p>データベースへの接続に失敗しました.</p>'.$mysqli->connect_error);
            exit();
        }
        $mysqli->select_db($dsn['db']);
        $mysqli->set_charset('utf8');

        for($i=0;$i<count($a);$i++){
                $keys[$i]=array_keys($a[$i]);
                $values[$i]=array_values($a[$i]);
                $q_insert[$i]="INSERT INTO t_result(t_result_".implode(",t_result_",$keys[$i]).") VALUES ('".implode("','",$values[$i])."')";
                $result3[$i]=$mysqli->query($q_insert[$i]);
        }
        $mysqli->close();
    }

function db_insert_from_indexUploader($a,$b){
       $mysqli= new mysqli($dsn['host'],$dsn['user'],$dsn['pass']);
        if ($mysqli->connect_errno) {
            print('<p>データベースへの接続に失敗しました.</p>'.$mysqli->connect_error);
            exit();
        }
        $mysqli->select_db($dsn['db']);
        $mysqli->set_charset('utf8');
        for($i=0;$i<count($a);$i++){
            $q_colCheck[$i]="DESCRIBE t_result $a[$i]";
            $result1[$i]=$mysqli->query($q_colCheck[$i]);
            $q_alter[$i]="ALTER TABLE t_result ADD t_result_$a[$i] FLOAT AFTER t_result_data";
            $result2[$i]=$mysqli->query($q_alter[$i]);
            if(!$result2[$i]){
                   // echo "error!!!!!";
            }
        }




        for($i=0;$i<count($b);$i++){
                $keys[$i]=array_keys($b[$i]);
                $values[$i]=array_values($b[$i]);
                $q_insert[$i]="INSERT INTO t_result(t_result_".implode(",t_result_",$keys[$i]).") VALUES ('".implode("','",$values[$i])."')";
                $result3[$i]=$mysqli->query($q_insert[$i]);
        }
        $mysqli->close();
}

function getExtension($filepath){
    $path_parts=pathinfo($filepath);
    return $path_parts['extension'];
}











