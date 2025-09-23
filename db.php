<?php
$host = "localhost";
$user = "root";      // default XAMPP MySQL user
$pass = "";          // default password is empty
$dbname = "health_portal";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>