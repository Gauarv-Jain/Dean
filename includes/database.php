<?php 

$dpHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "dean";

$conn = mysqli_connect($dpHost, $dbUser, $dbPass, $dbName);

if(!$conn) {
   die("Could not connect to database");
}

// try{
//    $con = new PDO("mysql:host=$dpHost;dbname=$dbName", $dbUser, $dbPass);

//    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    //echo "Connection Success";
//  }
//  catch (PDOException $e){
//    echo "Error in connection " . $e->getMessage();
//  }


?>