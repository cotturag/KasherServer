<?php 
include("db_connect.php");
session_start();

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
$fh = fopen('files/test.txt','r');
while ($line = fgets($fh)) { 
   echo($line);
}
fclose($fh);

$sql="select * from users";
$content= mysqli_query($conn,$sql);
if (mysqli_num_rows($content)>0){
    while ($row= mysqli_fetch_assoc($content)){
        echo strval($row["id"]);
    }
    
}




?>
