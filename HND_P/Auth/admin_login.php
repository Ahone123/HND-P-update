<?php
session_start();
require_once(__DIR__ . "/../config/db.php");

// make sure admin table exists
$create = "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($create);

// check whether any admin account exists yet
$countRes = $conn->query("SELECT COUNT(*) FROM admin");
$needSetup = true;
if ($countRes) {
    $countRow = $countRes->fetch_row();
    $needSetup = ((int)$countRow[0] === 0);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Email and password are required.';
    } else {
        if ($needSetup) {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $message = 'Name is required to create the first admin.';
            } else {
                // create new admin account
                $stmt = $conn->prepare("INSERT INTO admin (name,email,password) VALUES (?,?,?)");
                $stmt->bind_param('sss', $name, $email, $password);
                if ($stmt->execute()) {
                    $_SESSION['admin_id'] = $stmt->insert_id;
                    $_SESSION['admin_name'] = $name;
                    header('Location: ../Admin/dashboard.php');
                    exit;
                } else {
                    $message = 'Error creating admin: ' . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare("SELECT id, name, password FROM admin WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if ($row['password'] === $password) {
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_name'] = $row['name'];
                    header('Location: ../Admin/dashboard.php');
                    exit;
                } else {
                    $message = 'Invalid email or password.';
                }
            } else {
                $message = 'Invalid email or password.';
            }
            $stmt->close();
        }
    }
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

            <form method="POST" novalidate>
                <?php if (
                    $message
                ): ?>
                    <div class="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if ($needSetup): ?>
                    <p class="note">No admin found – create the initial account below.</p>
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter full name" required>
                <?php endif; ?>

                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit" name="login"><?php echo $needSetup ? 'Create Admin' : 'Sign In'; ?></button>
            </form>

            <a href="#" class="forgot">Forgot your password?</a>


        </div>

    </div>

</body>

</html>