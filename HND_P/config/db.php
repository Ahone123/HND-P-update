<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "waste_management";

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// connection established, no output so pages can control feedback

?>
