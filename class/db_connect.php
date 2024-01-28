<?php
$servername = "petstat-server.mysql.database.azure.com";
$username = "uauvivdpud";
$password = "74KQB1484N202EIS$";
$dbname = "petstatvan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
