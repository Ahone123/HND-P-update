

<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "waste_management";

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}else{
    
echo ("Database connected successfully");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $name = $_POST['Fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(!empty($name) && !empty($email) && !empty($password)){
        $sql = "INSERT INTO  admin (name,email,password) VALUES ('$name','$email','$password')";
        if($conn -> query(query: $sql) === TRUE) {
            echo "<p style='color:green'> NewRecord creayed succesfully </p>";
        }else{
            echo "<p style='color:red'> Error:".$conn -> error. "</p>";
        }
    }
} else{
    echo "<p style:'red'> please fill the fields </p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login for CleanCity</title>
    <link rel="stylesheet" href="login.css">

</head>

<body>

    <div class="login-wrapper">

        <div class="login-card">
            <!-- <div class="icon">♻</div> -->

            <h2>Admin Login</h2>
            <p>Sign in to your account</p>

            <form method="POST">
                <label>Name</label>
                <input type="text" name="Fullname" placeholder="Enter your full names">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>


                <button type="submit" name="login">Sign In</button>
                <div class="both">
        
        

            </form>

            <a href="#" class="forgot">Forgot your password?</a>


        </div>

    </div>

</body>

</html>