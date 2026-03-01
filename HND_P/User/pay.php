<?php
session_start();
require_once("../config/db.php");

// make sure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: pay.php');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

$standardFee = 50.00;
$message = '';

// get report id from GET (for display) or POST (for processing)
$reportId = isset($_GET['report']) ? (int)$_GET['report'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_report'])) {
    $reportId = (int) $_POST['pay_report'];
    // update only if this report belongs to user and is unpaid
    $amount = $standardFee;
    $update = "UPDATE reports SET payment_amount = ?, payment_status = 'Paid', paid_at = NOW() \
               WHERE id = ? AND user_id = ? AND payment_status = 'Unpaid'";
    if ($stmt = $conn->prepare($update)) {
        $stmt->bind_param('dii', $amount, $reportId, $user_id);
        if ($stmt->execute()) {
            $message = 'Payment recorded for report #' . $reportId;
        } else {
            $message = 'Error updating payment: ' . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    } else {
        $message = 'Database error: ' . htmlspecialchars($conn->error);
    }
    // redirect back to overview with message
    $param = urlencode($message);
    header("Location: payment.php?msg=$param");
    exit;
}

// optionally fetch report details for display (location, date, status)
$report = null;
if ($reportId) {
    $sql = "SELECT * FROM reports WHERE id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ii', $reportId, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $report = $result->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Payment | CleanCity</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="report_status.css?v=3">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<div class="topbar">
    <span id="menuToggle" class="menu-icon"><i class="fas fa-bars"></i></span>
    <h1>Make Payment</h1>
</div>
<div class="sidebar">
    <h2>♻ Clean City</h2>
    <ul>
        <li><a href="user_dashboard.php">🏠 Home</a></li>
        <li><a href="report_status.php">📄 My Reports</a></li>
        <li><a href="payment.php">💳 Payment Status</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</div>

<div class="main">
    <h1>Pay for Report #<?php echo htmlspecialchars($reportId); ?></h1>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!$report): ?>
        <p>Report not found or does not belong to you.</p>
    <?php elseif ($report['payment_status'] !== 'Unpaid'): ?>
        <p>This report has already been paid.</p>
    <?php else: ?>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($report['location']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($report['report_date']); ?></p>
        <p><strong>Amount due:</strong> FCFA <?php echo number_format($standardFee,2); ?></p>

        <form method="POST" class="payment-form">
            <input type="hidden" name="pay_report" value="<?php echo htmlspecialchars($reportId); ?>">

            <fieldset class="method-group">
                <legend>Choose method</legend>
                <label><input type="radio" name="method" value="mtn" required> <img src="../assets/mtn.png" alt="MTN" class="pm-logo"> MTN Mobile Money</label>
                <label><input type="radio" name="method" value="orange"> <img src="../assets/orange.png" alt="Orange" class="pm-logo"> Orange Money</label>
                <label><input type="radio" name="method" value="cash"> <img src="../assets/cash.png" alt="Cash" class="pm-logo"> Cash (FCFA)</label>
            </fieldset>

            <label>Cardholder Name</label>
            <input type="text" name="card_name" required>
            <label>Card Number</label>
            <input type="text" name="card_number" maxlength="16" required>
            <label>Expiry</label>
            <input type="text" name="card_expiry" placeholder="MM/YY" required>
            <label>CVV</label>
            <input type="text" name="card_cvv" maxlength="3" required>
            <button type="submit" class="pay-btn">Submit Payment (FCFA <?php echo number_format($standardFee,2); ?>)</button>
        </form>
    <?php endif; ?>
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