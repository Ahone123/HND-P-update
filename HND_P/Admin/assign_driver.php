<?php
session_start();
require_once('../config/db.php');

// admin guard
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../Auth/admin login.php');
    exit;
}

// ensure driver table exists
$createDrivers = "CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createDrivers);

// make sure reports table has necessary columns
$alter = "ALTER TABLE reports 
    ADD COLUMN IF NOT EXISTS location VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS driver_id INT NULL,
    ADD COLUMN IF NOT EXISTS before_image VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS after_image VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS notes TEXT NULL";
// MySQL prior to 8.0 does not allow IF NOT EXISTS in ALTER for columns; ignore errors
mysqli_query($conn, $alter);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die('Invalid report id');
}

// fetch report data
$stmt = $conn->prepare("SELECT r.*, u.name as user_name, d.name as driver_name FROM reports r
    LEFT JOIN users u ON r.user_id=u.id
    LEFT JOIN drivers d ON r.driver_id=d.id
    WHERE r.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();
if (!$report) {
    die('Report not found');
}

// fetch drivers list
$drivers = mysqli_query($conn, "SELECT * FROM drivers ORDER BY name");

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign'])) {
        $selected = intval($_POST['driver']);
        if ($selected > 0) {
            $stmt = $conn->prepare("UPDATE reports SET driver_id=?, status='Assigned' WHERE id=?");
            $stmt->bind_param('ii', $selected, $id);
            $stmt->execute();
            $feedback = 'Driver assigned successfully.';
            // reload report info
            header("Location: assign_driver.php?id=$id");
            exit;
        } else {
            $feedback = 'Please choose a driver.';
        }
    } elseif (isset($_POST['complete'])) {
        if (isset($_FILES['after']) && $_FILES['after']['error'] === UPLOAD_ERR_OK) {
            $img = time() . '_' . basename($_FILES['after']['name']);
            $target = __DIR__ . '/../uploads/after/' . $img;
            if (move_uploaded_file($_FILES['after']['tmp_name'], $target)) {
                $stmt = $conn->prepare("UPDATE reports SET status='Completed', after_image=? WHERE id=?");
                $stmt->bind_param('si', $img, $id);
                $stmt->execute();
                $feedback = 'Report marked completed.';
                header("Location: assign_driver.php?id=$id");
                exit;
            } else {
                $feedback = 'Failed to upload image.';
            }
        } else {
            $feedback = 'Please select an image.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Driver | CleanCity</title>
    <link rel="stylesheet" href="assign_driver.css">
</head>
<body>
<div class="assign-box">
    <h2>Report #<?php echo htmlspecialchars($report['id']); ?></h2>

    <?php if ($feedback): ?>
        <div class="alert"><?php echo htmlspecialchars($feedback); ?></div>
    <?php endif; ?>

    <div class="report-info">
        <p><strong>User:</strong> <?php echo htmlspecialchars($report['user_name']); ?></p>
        <?php if ($report['location']): ?><p><strong>Location:</strong> <?php echo htmlspecialchars($report['location']); ?></p><?php endif; ?>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($report['status']); ?></p>
        <?php if ($report['driver_name']): ?><p><strong>Driver:</strong> <?php echo htmlspecialchars($report['driver_name']); ?></p><?php endif; ?>
        <?php if ($report['before_image']): ?><p><strong>Before:</strong> <a href="../uploads/before/<?php echo htmlspecialchars($report['before_image']); ?>" target="_blank">view</a></p><?php endif; ?>
    </div>

    <?php if ($report['status'] === 'Pending' || $report['status'] === 'Assigned'): ?>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($report['status'] === 'Pending'): ?>
                <label>Select Driver</label>
                <select name="driver" required>
                    <option value="">-- choose --</option>
                    <?php while ($d = mysqli_fetch_assoc($drivers)): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="assign">Assign Driver</button>
            <?php endif; ?>

            <?php if ($report['status'] === 'Assigned'): ?>
                <label>Upload after-image</label>
                <input type="file" name="after" accept="image/*" required>
                <button type="submit" name="complete">Mark Completed</button>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <p>This report is already completed.</p>
    <?php endif; ?>

</div>
</body>
</html>