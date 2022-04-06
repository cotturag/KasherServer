<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">          
        <!--<script src="../tools/jquery-3.3.1.js"></script>   -->
        <script src="../bootstrap/js/bootstrap.bundle.js"></script> 
    </head>
    <body>        
        <form method="post" action="index.php">';


include("dbConnectRedbean.php");
include("login.php");
if (isset($_SESSION["loggeduser"])){   
    $loggedUser=$_SESSION["loggeduser"];
    $family=$loggedUser;//TODO ez iediglenes, mert nem minden felhasználóra igaz
    $selectFromSource="SELECT transactions.id, transactions.source,transactions.destination"
            . ",transactions.date,transactions.money,"
            . "transactions.details,transactions.transactiontype,"
            . "funds.name AS sourceName,users.name AS username FROM transactions,funds,users ";    
    $selectFromDestination="SELECT transactions.id, transactions.source,transactions.destination"
            . ",transactions.date,transactions.money,"
            . "transactions.details,transactions.transactiontype,"
            . "funds.name AS destinationName,users.name AS username FROM transactions,funds,users ";    
    $fromAccountsPrivate=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' 
               AND funds.type='1' ";               
    $fromAccountsSupervisedInserted=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND otherowner='".$loggedUser."' "
            . "AND hookedto > 0 "
            . "AND funds.type='3' ";            
    $fromAccountsSupervisedUpdated=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND hookedto=0 "
            . "AND otherowner='".$loggedUser."' "
            . "AND funds.type='3' "
            . "AND (SELECT COUNT(*) FROM funds ".$fromAccountsSupervisedInserted.")=0 ";
    $fromAccountsPublicAll=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND funds.type='2' ";
    
    $toCostCategoriesPrivate=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' 
               AND funds.type='A' ";        
    $toCostCategoriesSupervisedInserted=
             "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND otherowner='".$loggedUser."' "
            . "AND hookedto > 0 "
            . "AND funds.type='C' ";                  
    $toCostCategoriesSupervisedUpdated=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND hookedto=0 "
            . "AND otherowner='".$loggedUser."' "
            . "AND funds.type='C' "
            . "AND (SELECT COUNT(*) FROM funds ".$fromAccountsSupervisedInserted.")=0 ";
    $toCostCategoriesPublicAll= 
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND funds.type='B' ";
    
    $toAccountInDestinationSupervisedInserted=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND otherowner='".$loggedUser."' "
            . "AND hookedto > 0 "
            . "AND funds.type='3' "
            . "AND (transactions.transactiontype='3' OR transactions.transactiontype='2') ";    
    $toAccountInDestinationSuperVisedUpdated=  "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND hookedto=0 "
            . "AND otherowner='".$loggedUser."' "
            . "AND funds.type='3' "
            . "AND (transactions.transactiontype='3' OR transactions.transactiontype='2') "
            . "AND (SELECT COUNT(*) FROM funds ".$toAccountInDestinationSupervisedInserted.")=0 ";
           
    $toAccountInDestinationPublicAll=   "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND funds.type='2' "
            . "AND (transactions.transactiontype='3' OR transactions.transactiontype='2') ";
    
   
    
    $union= " UNION ALL ";            
       
    $sqlSource=
            $selectFromSource.$fromAccountsPrivate.
            $union.
            $selectFromSource.$fromAccountsSupervisedInserted.
            $union.
            $selectFromSource.$fromAccountsSupervisedUpdated.
            $union.
            $selectFromSource.$fromAccountsPublicAll;
     
    $sqlDestination=
            $selectFromDestination.$toCostCategoriesPrivate.
            $union.
            $selectFromDestination.$toCostCategoriesSupervisedInserted.
            $union.
            $selectFromDestination.$toCostCategoriesSupervisedUpdated.
            $union.           
            $selectFromDestination.$toCostCategoriesPublicAll.
            $union.                        
            $selectFromDestination.$toAccountInDestinationSupervisedInserted.
            $union.
            $selectFromDestination.$toAccountInDestinationSuperVisedUpdated.
            $union.
            $selectFromDestination.$toAccountInDestinationPublicAll;
            
            
            
     
    
    
            
         
     
   
   // echo $sqlSource;    
  
  //  echo $sqlDestination;   
   // echo "<br><br><br><br>";  
    
    $contentSource=R::getAll($sqlSource);       
    $contentDestination=R::getAll($sqlDestination);
    foreach($contentDestination as $key => $value){
        foreach($contentSource as $dkey => $dvalue) {
                if ($value["id"]==$dvalue["id"]){
                    $contentDestination[$key]["sourceName"]=$dvalue["sourceName"];
                    
                }
        }   
    }
        /*
        foreach ($value as $k=>$v){                
                $common[$key][$k]=$v;
                echo $k."->".$v."<br>";
            }  */
    /*$contentDestination=R::getAll($sqlDestination);
    foreach($contentDestination as $key => $value){            
                $common[$key]["destinationName"]=$value["sourceName"];
        }   
      
     */
   /*
    foreach($contentDestination as $key => $value){
        echo "<br>";
        foreach ($value as $k=>$v){                
                echo $k."->".$v."<br>";
            }  
            echo "<br>";
            
        }   
     */ 

    include("style.php");  
    echo '<div class="row">'
       . '<div class="col-sm-2"></div>'
       . '<div class="col-sm-8">';
    foreach($contentDestination as $key => $value){            
               switch ($value["transactiontype"]) {
                    case 1:{
                        include("cost.php");                        
                    }break;
                    case 2:{
                        include("incomes.php");
                    }break;
                    case 3:{
                        include("movements.php");
                    }break;
                }                 
            
        }
     
     
    echo '</div>'
       . '<div class="col-sm-2"></div>'
       . '</div>';
    echo "<br><br><Kilépés><input type='submit' name='logout' value='Kilépés'>";
}

echo "</form></body></html>";

 