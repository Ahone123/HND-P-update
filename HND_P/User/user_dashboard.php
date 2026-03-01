<?php
session_start();

require_once __DIR__ . '/../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/user login.php");
    exit();
}

// fetch counts for dashboard cards
$user_id = $_SESSION['user_id'];

// sample queries, adjust field names/status values if needed
$total_reports = 0;
$pending_count = 0;
$assigned_count = 0;
$completed_count = 0;

$sql = "SELECT COUNT(*) as total FROM reports WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($total);
    if ($stmt->fetch()) {
        $total_reports = $total;
    }
    $stmt->close();
}

// statuses assumed to be exactly 'Pending','Assigned','Completed'
foreach (['Pending' => 'pending_count', 'Assigned' => 'assigned_count', 'Completed' => 'completed_count'] as $status => $var) {
    $sql = "SELECT COUNT(*) as cnt FROM reports WHERE user_id = ? AND status = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('is', $user_id, $status);
        $stmt->execute();
        $stmt->bind_result($cnt);
        if ($stmt->fetch()) {
            $$var = $cnt;
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <meta name="description" content="Responsive user dashboard for waste reporting">
    <link rel="stylesheet" href="dashboard.css">
    <!-- include font-awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pVn+b6wv7+MZ8DkNL4I2KM7/3GwpwyqYVY8jVZQTQ6LdBi+p3JHUR4U+YJlj6EWvxbYOHrj66sqTKc0s5mqYxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- common JS (contains sidebar toggle and counters) -->
    <script src="../Auth/script.js" defer></script>
</head>

<body>
    <!-- top bar for mobile -->
    <div class="topbar">
        <span id="menuToggle" class="menu-icon">
            <i class="fas fa-bars"></i>
        </span>
        <h1>User Dashboard</h1>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Clean city</h2>

        <ul>
            <li class="active"><i class="fa-solid fa-house"></i> Dashboard</li>

            <li>
               <i class="bi bi-house-door-fill"></i> <a href="report_waste.php">
                    <i class="fa-solid fa-trash"></i> Report Waste
                </a>
            </li>

            <li>
                <a href="report_status.php">
                    <i class="fa-solid fa-file-lines"></i> My Reports
                </a>
            </li>

            <li>
                <a href="payment.php">
                    <i class="fa-solid fa-credit-card"></i> Payments
                </a>
            </li>

            <li>
                <a href="../Auth/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </li>
        </ul>

    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <h1>Welcome Back 👋</h1>
        <p class="subtitle">Manage your waste reports easily</p>

        <!-- STAT CARDS -->
        <div class="stats">

            <div class="card">
                <i class="fa-solid fa-file-lines fa-2x"></i>
                <h2 data-target="<?php echo $total_reports; ?>">0</h2>
                <p>Total Reports</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-hourglass-half fa-2x"></i>
                <h2 data-target="<?php echo $pending_count; ?>">0</h2>
                <p>Pending</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-truck fa-2x"></i>
                <h2 data-target="<?php echo $assigned_count; ?>">0</h2>
                <p>Assigned</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-check-circle fa-2x"></i>
                <h2 data-target="<?php echo $completed_count; ?>">0</h2>
                <p>Completed</p>
            </div>

        </div>

        <!-- QUICK ACTION -->
        <div class="quick-action">
            <h2>Quick Action</h2>
            <a href="report_waste.php" class="report-btn">+ Report New Waste</a>
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="recent">
            <h2>Recent Activity</h2>

            <?php
            // grab last 3 reports for user to show activity
            $recent_sql = "SELECT location, status, created_at FROM reports WHERE user_id = ? ORDER BY id DESC LIMIT 3";
            $has = false;
            if ($stmt = $conn->prepare($recent_sql)) {
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->bind_result($loc, $stat, $created);
                while ($stmt->fetch()) {
                    $has = true;
                    $emoji = '🗑';
                    if ($stat === 'Assigned') $emoji = '🚛';
                    elseif ($stat === 'Completed') $emoji = '✅';
                    // format date if you want to display it later
                    // $when = date('M j, Y', strtotime($created));
                    echo '<div class="activity">' . $emoji . ' Report at ' . htmlspecialchars($loc);
                    echo ' <span class="' . strtolower($stat) . '">' . htmlspecialchars($stat) . '</span>';
                    echo '</div>';
                }
                $stmt->close();
            }
            if (!$has) {
                echo '<div class="activity">You haven\'t submitted any reports yet.</div>';
            }
            ?>
        </div>

    </div>

</body>

</html>