<?php

session_start();

$json= file_get_contents("php://input");
$data= json_decode($json,true);
//$text = $data["name"];
foreach ($data as $key => $value){
    $cn=$key." ".$value;
}



file_put_contents ('files/test.txt', $cn);