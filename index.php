<?php 

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("dbConnectRedbean.php");



$sql="SELECT * FROM transactions";
$content=R::getAll($sql);
    
foreach ($content as $k => $v)   {
    foreach ($v as $key => $value){
        echo $value." | ";
    }
    echo "<br>";
}

/*
$sql="DELETE * FROM funds";
        R::exec($sql);
 
 */
?>
