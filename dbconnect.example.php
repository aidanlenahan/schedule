<?php
/*
This example dbconnect.php file works perfectly if you remove the '.example' from the filename and change the credentials of fly.io and/or LAMP/WAMP stack

*/
// Wrapper class to make PDO compatible with mysqli-style methods
class DatabaseConnection {
    private $connection;
    private $isPostgres;

    public function __construct($connection, $isPostgres = false) {
        $this->connection = $connection;
        $this->isPostgres = $isPostgres;
    }

    public function query($sql) {
        if ($this->isPostgres) {
            // PDO query
            $stmt = $this->connection->query($sql);
            return new DatabaseResult($stmt, true);
        } else {
            // mysqli query
            $result = $this->connection->query($sql);
            return new DatabaseResult($result, false);
        }
    }

    public function __get($name) {
        return $this->connection->$name;
    }

    public function __call($method, $args) {
        return call_user_func_array([$this->connection, $method], $args);
    }
}

class DatabaseResult {
    private $result;
    private $isPostgres;

    public function __construct($result, $isPostgres = false) {
        $this->result = $result;
        $this->isPostgres = $isPostgres;
    }

    public function fetch_assoc() {
        if ($this->isPostgres) {
            return $this->result->fetch(PDO::FETCH_ASSOC);
        } else {
            return $this->result->fetch_assoc();
        }
    }

    public function __get($name) {
        if ($name === 'num_rows') {
            if ($this->isPostgres) {
                return $this->result->rowCount();
            } else {
                return $this->result->num_rows;
            }
        }
        return $this->result->$name;
    }
}

// Auto-detect environment and connect accordingly
$isProduction = getenv('DB_HOST') !== false || getenv('FLY_APP_NAME') !== false;

if ($isProduction) {
    // Fly.io Managed Postgres Setup
    $host = getenv('DB_HOST') ?: '';
    $dbname = getenv('DB_NAME') ?: '';
    $user = getenv('DB_USER') ?: '';
    $pass = getenv('DB_PASS') ?: '';
    $port = getenv('DB_PORT') ?: '';

    try {
        // Connect using PDO for Postgres
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn = new DatabaseConnection($pdo, true);
        // echo "Connected to Postgres Successfully";
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage() . "<br>");
    }
} else {
    // Local setup with WAMP or LAMP stack (MySQL)
    $servername = "localhost";
    $username = "";
    $password = "";
    $dbname = "";

    $mysqli = new mysqli($servername, $username, $password, $dbname);

    if ($mysqli->connect_error){
        die("Database connection failed: " . $mysqli->connect_error . "<br>");
    }
    $conn = new DatabaseConnection($mysqli, false);
    // echo "Connected to MySQL Successfully";
}
?>
