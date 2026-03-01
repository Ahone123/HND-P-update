<?php
session_start();
require_once("../config/db.php");

// redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../User/user_dashboard.php');
    exit;
}

// ensure users table exists
$tableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $tableSql);

$message = '';

// success message after registering
if (isset($_GET['registered'])) {
    $message = 'Registration successful! Please log in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $message = 'Please fill in both fields.';
    } else {
        // check credentials
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                // success
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                header('Location: ../User/user_dashboard.php');
                exit;
            }
        }

        $message = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login | CleanCity</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="icon">♻</div>
        <h2>User Login</h2>
        <p>Sign in to your account</p>

        <?php if ($message): ?>
            <div class="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter your password" required>

            <button type="submit" name="login">Sign In</button>
        </form>

        <a href="register.php" class="forgot">Don't have an account? Register</a>
    </div>
</div>

</body>
</html>