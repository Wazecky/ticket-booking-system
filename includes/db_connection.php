<?php
// Database configuration
$servername = "j1r4n2ztuwm0bhh5.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
$username = "d30xlnm0y02a823r";
$password = "qha1s1wzfemt6cyg"; 
$database = "cs27gnq2fixwa61l";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
