<?php
// db-connect.php

$servername = "localhost";
$username = "root";
$password = "";  // your DB password
$dbname = "car_tradein";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
