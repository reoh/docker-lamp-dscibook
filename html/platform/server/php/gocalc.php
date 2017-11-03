<?php

    if(isset($_GET["button"])&&$_GET["button"]==="indexUpload"){
        echo $_GET["button"]."!!!";
        require 'calculate.php';
        echo '<pre>';
		print_r($scriptResult);
		echo '</pre>';

    }else{
        echo '<pre>';
		print_r($scriptResult);
		echo '</pre>';
    }
