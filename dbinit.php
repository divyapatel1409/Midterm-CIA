<?php
// phpinfo();  
$servername = "db"; // Your database server
$username = "root"; // Your database username
$password = "rootpassword"; // Your database password
$dbname = "midterm_assignment"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>