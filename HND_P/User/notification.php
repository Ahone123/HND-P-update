<?php
session_start();
require_once("../config/db.php");

// make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../User/notifications.php');
    exit;
}

// sanitize input
$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// ensure table exists
// $createTable = "CREATE TABLE IF NOT EXISTS notifications (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     user_id INT NOT NULL,
//     message VARCHAR(255) NOT NULL,
//     icon VARCHAR(10) DEFAULT '🔔',
//     type ENUM('success','info','warning','error') DEFAULT 'info',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     INDEX(user_id),
//     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createTable);

$query = "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | CleanCity</title>
    <link rel="stylesheet" href="notification.css">
</head>
<body>

<nav class="topbar">
    <a href="user_dashboard.php" class="back-btn">← Back to dashboard</a>
</nav>

<main class="container">
    <h1>Notifications</h1>

    <?php if ($result && mysqli_num_rows($result) > 0) : ?>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <div class="notification <?php echo htmlspecialchars($row['type'] ?? 'info'); ?>">
                <span><?php echo htmlspecialchars($row['icon'] ?? '🔔'); ?></span>
                <p><?php echo htmlspecialchars($row['message']); ?></p>
                <small class="time"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></small>
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <div class="no-notifications">
            <p>No notifications to display.</p>
        </div>
    <?php endif; ?>
</main>

</body>
</html>
