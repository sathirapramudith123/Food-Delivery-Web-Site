<?php
$host = "localhost";      // Change if you're not using localhost
$dbname = "food delivery db";  // Your database name
$username = "root";       // Your MySQL username
$password = "root";           // Your MySQL password (empty for XAMPP default)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
