<?php
session_start();
require_once("../config/db.php");

// require logged-in user
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Auth/user login .php');
    exit;
}
$user_id = (int) $_SESSION['user_id'];

// fetch reports securely
$query = "SELECT * FROM reports WHERE user_id = ? ORDER BY id DESC";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Reports | CleanCity</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="report_status.css?v=2">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pVn+b6wv7+MZ8DkNL4I2KM7/3GwpwyqYVY8jVZQTQ6LdBi+p3JHUR4U+YJlj6EWvxbYOHrj66sqTKc0s5mqYxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<!-- top bar for mobile -->
<div class="topbar">
    <span id="menuToggle" class="menu-icon"><i class="fas fa-bars"></i></span>
    <h1>My Waste Reports</h1>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h2>♻ Clean City</h2>
    <ul>
        <li><a href="user_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li class="active"><i class="fa-solid fa-file-lines"></i> My Reports</a></li>
        <li><a href="payment.php"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
        <li><a href="../Auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">

    <h1>My Waste Reports</h1>

    <div class="reports-container">

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="report-card">
                    <h3>Report <?php echo htmlspecialchars($row['id']); ?></h3>
                    <p><b>Location:</b> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><b>Type:</b> <?php echo !empty($row['waste_type']) ? htmlspecialchars($row['waste_type']) : '&ndash;'; ?></p>
                    <p><b>Date:</b> <?php
                        if (!empty($row['created_at'])) {
                            echo date('j M Y', strtotime($row['created_at']));
                        } else {
                            echo '&ndash;';
                        }
                    ?></p>
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="waste image" class="report-img" />
                    <?php endif; ?>
                    <span class="status <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                    <?php if (isset($row['payment_status']) && $row['payment_status'] === 'Unpaid'): ?>
                        <p><a href="payment.php?report=<?php echo $row['id']; ?>" class="pay-link">Pay for this report</a></p>
                    <?php endif; ?>
                    <?php if(isset($row['payment_status']) && $row['payment_status'] === 'Unpaid'): ?>
                        <p><a href="payment.php?report=<?php echo $row['id']; ?>" class="pay-link">Pay for this report</a></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reports found.</p>
        <?php endif; ?>

    </div>

</div>

<script>
// sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    btn.addEventListener('click', ()=>{
        sidebar.classList.toggle('open');
    });
});
</script>

</body>
</html>
