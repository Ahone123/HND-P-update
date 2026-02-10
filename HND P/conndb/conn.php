<?php
$hostname = "localhost";
$root = "root";
$dbname ="manu";
$password = "";

$conn = new mysqli("localhost","root","manu","");
if($conn->error)
    die("connect_error", $error)
//insert
if (isset($_POST['cards'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query(query;"INSERT into user"(name,email) VALUES('$name','email'))
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