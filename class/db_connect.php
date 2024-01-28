<?php
$servername = "tcp:petstat.database.windows.net,1433";
$username = "van";
$password = "As@dawe123";
$dbname = "petstatvan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
