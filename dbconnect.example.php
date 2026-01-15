<?php
$servername = "localhost";
$username = "user";
$password = "password";
$dbname = "rbrschedule";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error){
    die("Database connection failed" .$conn->connect_error . "<br>");
}



# echo "Connected Successfully";

?>