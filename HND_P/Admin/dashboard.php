
<?php
session_start();
require_once('../config/db.php');

// ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../Auth/admin_login.php');
    exit;
}

// make sure reports table exists (optional)
$createReports = "CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('Pending','Validated','Assigned','Completed') DEFAULT 'Pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createReports);

// stats
$totalReports = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reports"))[0];
$pendingReports = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reports WHERE status='Pending'"))[0];
$validatedReports = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reports WHERE status='Validated'"))[0];
$assignedTasks = (int) mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reports WHERE status='Assigned'"))[0];

// recent reports (limit 5)
$recentSql = "SELECT reports.id, users.name, reports.status, reports.created_at
    FROM reports JOIN users ON reports.user_id=users.id
    ORDER BY reports.created_at DESC LIMIT 5";
$recentReports = mysqli_query($conn, $recentSql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CleanCity</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .subtitle { color: #666; margin-bottom: 20px; }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>Admin dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="assign_driver.php">Assignments</a></li>
                <li><a href="../Auth/logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main">
            <h1>Welcome Back 👋</h1>
            <p class="subtitle">Manage your waste reports easily</p>

            <!-- STATS -->
            <div class="cards">
                <div class="card">Total Reports<br><strong><?php echo $totalReports; ?></strong></div>
                <div class="card">Pending Reports<br><strong><?php echo $pendingReports; ?></strong></div>
                <div class="card">Validated<br><strong><?php echo $validatedReports; ?></strong></div>
                <div class="card">Assigned Tasks<br><strong><?php echo $assignedTasks; ?></strong></div>
            </div>

            <!-- RECENT REPORTS -->
            <div class="reports">
                <h3>Recent Reports</h3>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($recentReports && mysqli_num_rows($recentReports) > 0): ?>
                            <?php while ($r = mysqli_fetch_assoc($recentReports)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['id']); ?></td>
                                    <td><?php echo htmlspecialchars($r['name']); ?></td>
                                    <td><?php echo htmlspecialchars($r['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No recent reports.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>

</html>