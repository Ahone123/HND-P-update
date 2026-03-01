<?php
$host = "localhost";
$user = "root";
$password ="";
$database = "waste_management";

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
    die("Connection failed: " .$connect_error);
}

echo ("Database connected successfully");
//insert
if (isset($_POST['cards'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query(query;"INSERT into user_table"(name,email) VALUES('$name','$email'))
}

//delete
if (isset($_GET['delete'])){
    $id=$_GET['delete'];
    $conn->query:"DELETE FROM users WHERE id=$id"
}

//update
if (isset($_POST['cards'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query(query;"UPDATE  user"(name,email) VALUES('$name','email'))
}

//edit
$edit=false
if (isset($_GET['edit'])){
    $edit=true;
    $id=$_GET['edit'];
    $result=$conn->query
}
?>