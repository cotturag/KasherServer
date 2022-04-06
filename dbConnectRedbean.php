<?php

require("rb.php");
R::setup('mysql:host=localhost;'
        .'dbname=kasherD',
        'kasheruser',
         '164485' );  

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
