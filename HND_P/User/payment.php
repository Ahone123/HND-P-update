
<?php
session_start();
require_once("../config/db.php");

// create required columns if missing; failed queries will be ignored
$alterFields = "ALTER TABLE reports ADD COLUMN payment_amount DECIMAL(8,2) DEFAULT 0, ADD COLUMN payment_status VARCHAR(20) DEFAULT 'Unpaid', ADD COLUMN paid_at DATETIME NULL";
if (!mysqli_query($conn, $alterFields)) {
    // ignore duplicate column errors (code 1060) otherwise log
    $err = mysqli_errno($conn);
    if ($err !== 1060) {
        error_log("Alter reports failed: " . mysqli_error($conn));
    }
}

// require user logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Auth/user login .php');
    exit;
}
$user_id = (int) $_SESSION['user_id'];

$message = isset($_GET['msg']) ? $_GET['msg'] : '';

// flat fee applied to reports (could come from settings table later)
$standardFee = 50.00;

// totals will be calculated after we fetch rows
$reports = [];
$totalDue = 0;
$totalPaid = 0;


// fetch reports into array so we can compute totals before output
$query = "SELECT * FROM reports WHERE user_id = ? ORDER BY id DESC";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
        $amt = isset($row['payment_amount']) ? floatval($row['payment_amount']) : 0;
        if ($row['payment_status'] === 'Unpaid') {
            $totalDue += $standardFee;
        } else {
            $totalPaid += $amt;
        }
    }
    // rewind result pointer if necessary (not used further)
} else {
    $result = false;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Status | CleanCity</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="report_status.css?v=3">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pVn+b6wv7+MZ8DkNL4I2KM7/3GwpwyqYVY8jVZQTQ6LdBi+p3JHUR4U+YJlj6EWvxbYOHrj66sqTKc0s5mqYxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<!-- top bar for mobile -->
<div class="topbar">
    <span id="menuToggle" class="menu-icon"><i class="fas fa-bars"></i></span>
    <h1>Payment Status</h1>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h2>♻ Clean City</h2>
    <ul>
        <li><a href="user_dashboard.php">🏠 Home</a></li>
        <li><a href="report_status.php">📄 My Reports</a></li>
        <li class="active">💳 Payment Status</li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">

    <h1>My Waste Reports</h1>

    <div class="actions">
        <a href="payment.php" class="btn-pay-overview"><i class="fa-solid fa.money-bill-wave"></i> View All Payments</a>
    </div>

    <div class="reports-container">

        <?php
        if($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif;


        if (!empty($reports)){
            foreach ($reports as $row) {
                $amt = isset($row['payment_amount']) ? floatval($row['payment_amount']) : 0;
                $paidAmt = number_format($amt,2);
                $dueAmt = $row['payment_status'] === 'Unpaid' ? number_format($standardFee,2) : $paidAmt;
        ?>

        <div class="report-card">
            <h3>Report #<?php echo $row['id']; ?></h3>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($row['report_date']); ?></p>
            <p><strong>Payment status:</strong> <?php echo htmlspecialchars($row['payment_status']); ?>
                <?php if($row['payment_status'] === 'Unpaid'): ?>
                    <a href="pay.php?report=<?php echo $row['id']; ?>" class="pay-btn">Pay FCFA <?php echo $dueAmt; ?></a>
                <?php else: ?>
                    &ndash; paid ₦<?php echo $paidAmt; ?>
                <?php endif; ?>
            </p>
            <span class="status <?php echo strtolower($row['status']); ?>">
                <?php echo htmlspecialchars($row['status']); ?>
            </span>
        </div>

        <?php
            }

            // summary after loop
            if ($totalDue || $totalPaid) {
                echo '<div class="summary">';
                echo '<p>Total due: FCFA ' . number_format($totalDue,2) . '</p>';
                echo '<p>Total paid: FCFA ' . number_format($totalPaid,2) . '</p>';
                echo '</div>';
            }

        } else {
            echo "<p>No reports found.</p>";
        }
        ?>

    </div>

</div>

<script>
// mobile sidebar toggle
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    if(btn && sidebar){
        btn.addEventListener('click',()=>{
            sidebar.classList.toggle('open');
        });
    }
});
</script>

</body>
</html>