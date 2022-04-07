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
        <form method="post" action="index.php">
            <script type="text/javascript">    
                function showLogout(){
                    document.getElementById("logout").style.visibility="visible";                    
                }
              
             
                
            </script>
            <div class="row" style="margin:0;padding:0;">
                <div class="col-sm-2" style="margin:0;padding:0;"></div>
                <div class="col-sm-8" style="background-color: #EAEA7F;height:50px;margin:0;padding:0;border: 1px;">                    
                    <div class="row" style="margin:0;padding:0;">
                        <div class="col-sm-10" style="margin:0;padding:0;">
                            <span style="padding-left:10px;font-size: 20pt;">Kasher</span>
                            <span style="padding-left:10px;font-size: 14pt;">Családi pénzügyi nyilvántartó</span>                    
                        </div>
                        <div class="col-sm-1" style="margin:0;padding:0;"></div>
                        <div class="col-sm-1" style="margin:0;padding:0;">
                            <input type="submit" name="logout" value="Kilépés" style="visibility:hidden;" id="logout">                            
                        </div>
                    </div>                                        
                </div>
                <div class="col-sm-2" style="margin:0;padding:0;"></div>
            </div>';      

include("dbConnectRedbean.php");
include("login.php");

if (isset($_SESSION["loggeduser"])){   
    $loggedUser=$_SESSION["loggeduser"];
    echo '<script type="text/javascript">
            showLogout();            
          </script>';

    $familyContent=R::getAll("SELECT family FROM users WHERE id='".$loggedUser."'");
    foreach($familyContent as $key => $value){        
            $family=$value["family"];
        }
        
    
    
    $selectFromSource="SELECT transactions.id, transactions.source,transactions.destination"
            . ",transactions.date,transactions.money,"
            . "transactions.details,transactions.transactiontype,"
            . "funds.name AS sourceName,users.name AS username FROM transactions,funds,users ";    
    $selectFromDestination="SELECT transactions.id, transactions.source,transactions.destination"
            . ",transactions.date,transactions.money,"
            . "transactions.details,transactions.transactiontype,"
            . "funds.name AS destinationName,users.name AS username FROM transactions,funds,users ";    
    //olyan tranzakciókat kér le ami privát számlákat tartalmaz
    $fromAccountsPrivate=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND owner='".$loggedUser."' "
            . "AND funds.type='1' ";               
    //olyan tranzakciókat kér le, ahol a felvett közös és a felügyelt számlákból azok vannak,
    // ahol a felvételnél új példányt kellett beszúrni
    $fromAccountsPublicAndSupervisedInserted=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND otherowner='".$loggedUser."' "
            . "AND hookedto > 0 "
            . "AND (funds.type='2' OR funds.type='3') ";            
    //olyan tranzakciókat kér le, ahol a felvett közös és a felügyelt számlákból azok vannak,
    //ahol az owner az otherownerhez lett updatelve
    $fromAccountsPublicAndSupervisedUpdated=
              "WHERE transactions.source=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND hookedto=0 "
            . "AND otherowner='".$loggedUser."' "
            . "AND (funds.type='2' OR funds.type='3') "
            . "AND (SELECT COUNT(*) FROM funds ".$fromAccountsPublicAndSupervisedInserted.")=0 ";
    
    
    //azokat a tranzakciókat kéri le ahol privát költségkategóriák vannak
    $toCostCategoriesPrivate=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND owner='".$loggedUser."' "
            . "AND funds.type='A' ";        
    //olyan tranzakciókat kér le, ahol a felvett közös és a felügyelt költségkategóriákból azok vannak,
    // ahol a felvételnél új példányt kellett beszúrni
    $toCostCategoriesPublicAndSupervisedInserted=
             "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND otherowner='".$loggedUser."' "
            . "AND hookedto > 0 "
            . "AND (funds.type='B' OR funds.type='C') ";                  
    //olyan tranzakciókat kér le, ahol a felvett közös és a felügyelt költségkategóriákból azok vannak,
    //ahol az owner az otherownerhez lett updatelve
    $toCostCategoriesPublicAndSupervisedUpdated=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND hookedto=0 "
            . "AND otherowner='".$loggedUser."' "
            . "AND (funds.type='B' OR funds.type='C') "
            . "AND (SELECT COUNT(*) FROM funds ".$fromAccountsPublicAndSupervisedInserted.")=0 ";
    
    //itt privát számlákat tartalmazó tranzakciókat kér le bevételkor és pénzmozgáskor
    //(amelyek a destinationnál fordulnak elő)    
    $toAccountInDestinationPrivate=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND owner='".$loggedUser."' "
            . "AND funds.type='1' "
            . "AND (transactions.transactiontype='2' OR transactions.transactiontype='3') ";       
    //itt felvett közös és felügyelt számlákat tartalmazó tranzakciókat kér le bevételkor és pénzmozgáskor
    //(amelyek a destinationnál fordulnak elő)    
    // (beszúrás)
    $toAccountInDestinationPublicAndSupervisedInserted=
              "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND otherowner='".$loggedUser."' "
            . "AND hookedto > 0 "
            . "AND (funds.type='2' OR funds.type='3') "
            . "AND (transactions.transactiontype='3' OR transactions.transactiontype='2') ";    
    //itt felvett közös és felügyelt számlákat tartalmazó tranzakciókat kér le bevételkor és pénzmozgáskor,
    //(amelyek a destinationnál fordulnak elő)    
    // (updateelés)
    $toAccountInDestinationPublicAndSuperVisedUpdated=  "WHERE transactions.destination=funds.idfd "
            . "AND funds.owner=users.id "
            . "AND funds.family='".$family."' "
            . "AND hookedto=0 "
            . "AND otherowner='".$loggedUser."' "
            . "AND (funds.type='2' OR funds.type='3') "
            . "AND (transactions.transactiontype='3' OR transactions.transactiontype='2') "
            . "AND (SELECT COUNT(*) FROM funds ".$toAccountInDestinationPublicAndSupervisedInserted.")=0 ";
           
    
   
    
    $union= " UNION ALL ";  
    //$orderBy=" order BY transaction.date ";
       
    //azok a tranzakciók, ahol privát számlák, felvett közös és felügyelt számlák vannak
    //itt nem jelenik meg költségkategóriát tartalmazó tranzakció
    $sqlSource=
            $selectFromSource.$fromAccountsPrivate.
            $union.
            $selectFromSource.$fromAccountsPublicAndSupervisedInserted.
            $union.
            $selectFromSource.$fromAccountsPublicAndSupervisedUpdated;
      
    
    //azok a tranzakciók, ahol privát költségkategóriák, felvett közös és felügyelt költségkategóriák vannak
    //itt megjelenik privát, felvett közös és felügyelt számlát tartalmazó tranzakció
    //(a destination-nél)
    $sqlDestination=
            $selectFromDestination.$toCostCategoriesPrivate.
            $union.
            $selectFromDestination.$toCostCategoriesPublicAndSupervisedInserted.
            $union.
            $selectFromDestination.$toCostCategoriesPublicAndSupervisedUpdated.
            $union.                       
            $selectFromDestination.$toAccountInDestinationPrivate.
            $union.
            $selectFromDestination.$toAccountInDestinationPublicAndSupervisedInserted.
            $union.
            $selectFromDestination.$toAccountInDestinationPublicAndSuperVisedUpdated;
            
            
          
    
    $contentSource=R::getAll($sqlSource);       
    $contentDestination=R::getAll($sqlDestination);
    //összefésülöm a kettőt így a költségkategóriás tömb teljes tranzakciókat
    //fog tartalmazni
    foreach($contentDestination as $key => $value){
        $equal=false;
        foreach($contentSource as $dkey => $dvalue) {
                if ($value["id"]==$dvalue["id"]){
                    $contentDestination[$key]["sourceName"]=$dvalue["sourceName"];
                    $equal=true;                
                }                
        }
        if ($contentDestination[$key]["transactiontype"]<>"2"&&!$equal) unset($contentDestination[$key]);
    }
    //az összefésülés logikája, hogy a költségkategóriákat tartalmazó asszociatív
    //tömb minden elemének az id mezőjét összehasonlítom a számlákat tartalmazó
    //asszociatív tömb minden elemének id mezőjével, egyezés esetén beírom a
    //költségkategóriás tömbbe a "sourceName" mezőbe számlás tömbben lévő
    //"sourceName"-et, így a költségkategóriás tömb teljes tranzakciókat fog
    //tartalmazni, ha nem talált a költségkategóriás tömb eleméhez passzoló
    //id-jű számlás tömböt, akkor vagy bevétel zajlott le, vagy a 
    //a költségkategóriás tömbben a destinationnél olyan számla vagy költségkategória
    //szerepel, amire olyan számláról lett tranzakció indítva, ami nincs felvéve
    //tehát ezeket a tranzakciókat kitörlöm a költségkategóriás tömbből
    //de csak akkor törlök ha nem bevétel történt
    
    
    function sortArray($my_array)
    {
	for($i=0;$i<count($my_array);$i++){
		$val = $my_array[$i]["date"];
		$j = $i-1;
		while($j>=0 && $my_array[$j]["date"] > $val){
			$my_array[$j+1]["date"] = $my_array[$j]["date"];
			$j--;
		}
		$my_array[$j+1]["date"] = $val;
	}
    return $my_array;
    }
    
    
    $sortedTransactions= sortArray($contentDestination);
    $reversedTransaction= array_reverse($sortedTransactions);
    
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
       . '<div class="col-sm-8" style="text-align:center;">'
            . 'Tranzakciók: ';if (isset($_SESSION["loggeduser"])) echo $_SESSION["loggeduser"];
         echo '';
    foreach($reversedTransaction as $key => $value){            
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
    
}

echo "</form></body></html>";

 