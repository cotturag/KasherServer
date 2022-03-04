<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$serverName = "localhost";
$userName = "kasheruser";
$password = "164485";
$dbname= "kasherD";


$conn = mysqli_connect($serverName, $userName, $password,$dbname);


?>

