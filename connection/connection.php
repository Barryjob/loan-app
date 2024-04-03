<?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
    header("Acces-Contorl-Allow-Origin");/// to call API and clear the error from web-page

   
    /////////////////////////////////////////////////////////////////

    // Database Configuration //
    $_HOST_NAME = "localhost";
    $_DB_USERNAME= "root";
    $_DB_PASSWORD= "";

    // Create Connection //
    $conn = mysqli_connect($_HOST_NAME, $_DB_USERNAME, $_DB_PASSWORD) or die("connection error");
    mysqli_select_db($conn, "bcom_loan");

?>

