<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// ensure reports table exists
$ensure = "CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    location VARCHAR(255),
    waste_type VARCHAR(255) DEFAULT '',
    image_path VARCHAR(255) DEFAULT '',
    status VARCHAR(30) DEFAULT 'Pending',
    payment_amount DECIMAL(8,2) DEFAULT 0,
    payment_status VARCHAR(20) DEFAULT 'Unpaid',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX(user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $ensure);

// ensure required columns exist; older MySQL may not support IF NOT EXISTS so run one by one
$cols = [
    "waste_type VARCHAR(255) DEFAULT ''",
    "image_path VARCHAR(255) DEFAULT ''",
    "created_at DATETIME DEFAULT CURRENT_TIMESTAMP"
];
foreach ($cols as $col) {
    $sql = "ALTER TABLE reports ADD COLUMN $col";
    if (!mysqli_query($conn, $sql)) {
        $code = mysqli_errno($conn);
        // ignore duplicate column errors
        if ($code !== 1060) {
            error_log("adding column failed ($col): " . mysqli_error($conn));
        }
    }
}
// an `uploads/` directory will be created automatically when the first
// image is uploaded; files are referenced relative to this script.

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Auth/user login .php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id   = (int) $_SESSION['user_id'];
    $location  = trim($_POST['location'] ?? '');
    $waste_type = trim($_POST['waste_type'] ?? '');

    // handle image upload
    $imagePath = '';
    if (isset($_FILES['waste_image']) && $_FILES['waste_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['waste_image']['name'], PATHINFO_EXTENSION);
        $targetDir = __DIR__ . '/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $filename = uniqid('waste_', true) . '.' . $ext;
        if (move_uploaded_file($_FILES['waste_image']['tmp_name'], $targetDir . $filename)) {
            $imagePath = 'uploads/' . $filename;
        }
    }

    $status = 'Pending';
    // created_at column has default CURRENT_TIMESTAMP so we don't need to insert it explicitly
    $sql = "INSERT INTO reports (user_id, location, waste_type, image_path, status) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('issss', $user_id, $location, $waste_type, $imagePath, $status);
        if ($stmt->execute()) {
            $message = 'Report submitted successfully!';
        } else {
            $message = 'Failed to submit report: ' . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    } else {
        $message = 'Database error: ' . htmlspecialchars($conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Waste</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="report_waste.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pVn+b6wv7+MZ8DkNL4I2KM7/3GwpwyqYVY8jVZQTQ6LdBi+p3JHUR4U+YJlj6EWvxbYOHrj66sqTKc0s5mqYxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="../Auth/script.js" defer></script>
</head>
<body>

<!-- top bar for mobile -->
<div class="topbar">
    <span id="menuToggle" class="menu-icon"><i class="fas fa-bars"></i></span>
    <h1>Report New Waste</h1>
</div>

<!-- top bar for mobile -->
<div class="topbar">
    <span id="menuToggle" class="menu-icon"><i class="fas fa-bars"></i></span>
    <h1>Report New Waste</h1>
</div>

<div class="sidebar">
    <h2>♻ Clean City</h2>
    <ul>
        <li><a href="user_dashboard.php">🏠 Home</a></li>
        <li class="active">🗑 Report Waste</li>
        <li><a href="report_status.php">📄 My Reports</a></li>
        <li><a href="payment.php">💳 Payments</a></li>
        <li><a href="../Auth/logout.php">🚪 Logout</a></li>
    </ul>
</div>

<div class="main">
    <h1>Report New Waste</h1>

    <?php if ($message): ?>
        <div class="feedback"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="report-form">
        <label for="location">Location or Description</label>
        <input type="text" id="location" name="location" required>

        <label for="waste_type">Type of Waste</label>
        <input type="text" id="waste_type" name="waste_type" placeholder="e.g. Plastic bottles, Food waste" required>

        <label for="waste_image">Upload Image</label>
        <input type="file" id="wasteImage" name="waste_image" accept="image/*">
        <img id="preview" src="" alt="preview" class="preview" />

        <button type="submit" class="submit-btn">Submit Report</button>
    </form>
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
