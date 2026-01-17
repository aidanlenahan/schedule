<?php
/* local setup with WAMP or LAMP stack
$servername = "localhost";
$username = "user";
$password = "password";
$dbname = "rbrschedule";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error){
    die("Database connection failed" .$conn->connect_error . "<br>");
}



# echo "Connected Successfully";
*/



/* Fly.io Managed Postgres Setup
CHANGE THESE VALUES */
$host = getenv('DB_HOST') ?: 'hostname_here';
$dbname = getenv('DB_NAME') ?: 'dbname_here';
$user = getenv('DB_USER') ?: 'user_here';
$pass = getenv('DB_PASS') ?: 'pass_here';
$port = getenv('DB_PORT') ?: 'port_here';

try {
    // Connect using PDO
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: echo "Connected Successfully";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "<br>");
}
?>
