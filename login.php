<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["logout"])){        
        unset($_SESSION["loggeduser"]);
        
    }
    if (isset($_POST['username'])&&isset($_POST["password"])){
        $user=$_POST['username'];        
        $password=$_POST['password'];

        $res=R::getAll("SELECT password FROM users WHERE id='".$user."'");
        foreach($res as $key => $value){
            foreach ($value as $k=>$v){                
                $pass=$v;                                
            }    
        }
        if ($password==$pass){          
            $_SESSION["loggeduser"]=$user;   
        }            
    }
}
if (!isset($_SESSION["loggeduser"])){
    include("loginDialog.php");
}

