<?php
session_start();
include("dbConnectRedbean.php");

$json= file_get_contents("php://input");
$data= json_decode($json,true);

if ($data["method"]<>''){
$method=$data["method"];
unset($data["method"]);

if ($method=="insertFund"){
    $idfd=$data["id"];
    unset($data["id"]);
    $funds=R::dispense('funds');
    foreach ($data as $key => $value){
        $funds -> $key = $value;
    }
    $funds -> idfd = $idfd;
    R::store($funds);
    
    
}
if ($method=="updateFund"){
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
if ($method=="deleteFund"){
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
if ($method=="insertTransaction"){
    $transactions=R::dispense("transactions");
    $idta=$data["id"];
    unset($data["id"]);
  
    foreach($data as $key=>$value){
        $transactions -> $key = $value;
    }
   
    $transactions -> idta = $idta;
    R::store($transactions);
    
}
if ($method=="deleteTransaction"){
   
}
/*
foreach ($data as $key => $value){
    $cn=$key." ".$value."\n";
    $cnk=$cnk.$cn;
}
*/


  foreach ($transaction as $key => $value){
    $cn=$key." ".$value."\n";
    $cnk=$cnk.$cn;
}
  

 

file_put_contents ('files/test.txt', $cnk);

    
}else {
    
    $operate=$data["operate"];
    $family=$data["arg1"];
    
    if ($operate=="deletefunds"){
        
        file_put_contents ('files/message.txt', $family);
        $sql="DELETE FROM funds WHERE family='".$family."'";
        R::exec($sql);
        
    }
}










