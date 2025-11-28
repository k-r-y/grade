<?php
// db_connect.php

$host = "localhost";       // usually localhost
$user = "root";            // your MySQL username
$pass = "";                // your MySQL password (if any)
$dbname = "portal";        // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
