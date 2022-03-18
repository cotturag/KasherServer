<?php
session_start();
include("dbConnectRedbean.php");

$json= file_get_contents("php://input");
$data= json_decode($json,true);

$method=$data["method"];
unset($data["method"]);

if ($method=="insert"){
    $idfd=$data["id"];
    unset($data["id"]);
    $funds=R::dispense('funds');
    foreach ($data as $key => $value){
        $funds -> $key = $value;
    }
    $funds -> idfd = $idfd;
    R::store($funds);
    
    
}
if ($method=="update"){
    $funds=R::dispense('funds');
    $idfd=$data["id"];
    unset($data["id"]);
    $family=$data["family"];
    $sql="SELECT id FROM funds WHERE idfd=".$idfd." AND family='".$family."'";    
    $content=R::getAll($sql);
    $res=0;
    foreach ($content[0] as $k => $v){
        $res=$v;    
    }   
    foreach ($data as $key => $value){
        $funds -> $key = $value;
    }
    $funds -> idfd = $idfd;
    $funds -> id = $res;
    R::store($funds);
    
    
    
}
if ($method=="delete"){
    $funds=R::dispense('funds');
    $idfd=$data["id"];
    unset($data["id"]);
    $family=$data["family"];
    $sql="SELECT id FROM funds WHERE idfd=".$idfd." AND family='".$family."'";    
    $content=R::getAll($sql);
    $res=0;
    foreach ($content[0] as $k => $v){
        $res=$v;    
    }   
    foreach ($data as $key => $value){
        $funds -> $key = $value;
    }
    $funds -> idfd = $idfd;
    $funds -> id = $res;
    R::trash($funds);
    
    
}



foreach ($data as $key => $value){
    $cn=$key." ".$value."\n";
    $cnk=$cnk.$cn;
}

/*
foreach ($funds as $key => $value){
    $cn=$key." ".$value."\n";
    $cnk=$cnk.$cn;
}
 
 */
file_put_contents ('files/test.txt', $cnk);











