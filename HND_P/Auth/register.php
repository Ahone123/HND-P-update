
<?php
session_start();
require_once("../config/db.php");

// redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../User/user_dashboard.php');
    exit;
}

// ensure users table exists (same as login page)
$tableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $tableSql);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($name === '' || $email === '' || $password === '') {
        $message = 'All fields are required.';
    } else {
        // insert user with hashed password
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) {
            header('Location: user login .php?registered=1');
            exit;
        } else {
            $message = 'Error creating account: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration | CleanCity</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<div class="register-wrapper">
    <div class="register-card">
        <div class="icon">♻</div>
        <h2>User Registration</h2>
        <p>Create your account</p>

        <?php if ($message): ?>
            <div class="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="name">Full name</label>
            <input id="name" type="text" name="fullname" placeholder="Enter your full name" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Create a password" required>

            <button type="submit" name="register">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="user login .php">Login</a>
        </div>

    </div>
</div>

</body>
</html>
