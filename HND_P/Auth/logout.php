<?php
// logout.php - destroy session and show confirmation
session_start();

// clear all session data
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// after destroying, you can redirect or show message
// use meta refresh to auto-redirect to login after a few seconds
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out | CleanCity</title>
    <meta http-equiv="refresh" content="5;url=../Auth/user login .php">
    <style>
        * {box-sizing: border-box; margin:0; padding:0;}
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            max-width: 90%;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        @media (max-width:480px){
            .box {padding:20px;}
            a {padding:10px 20px;}
        }
    </style>
</head>
<body>

<div class="box">
    <h2>You have been logged out</h2>
    <p>Thank you for using CleanCity.</p>
    <p>You will be redirected to the login page shortly.</p>
    <a href="../Auth/user login .php">Go to login now</a>
</div>

</body>
</html>
