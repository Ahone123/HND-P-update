<?php
session_start();
require_once('../config/db.php');

// simple admin access check (expand as needed)
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../Auth/admin_login.php');
    exit;
}

$query = "SELECT reports.*, users.name FROM reports
JOIN users ON reports.user_id = users.id
ORDER BY reports.id DESC";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Waste Reports</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .table-wrapper { overflow-x: auto; }
        .status-completed { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; }
        .status-assigned { color: #17a2b8; }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- sidebar could be included here if you have one -->
    <div class="main">
        <div class="top-bar">
            <h2>All Waste Reports</h2>
            <a href="dashboard.php" class="btns">← Dashboard</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                        $statusClass = '';
                        switch (strtolower($row['status'])) {
                            case 'completed': $statusClass = 'status-completed'; break;
                            case 'pending': $statusClass = 'status-pending'; break;
                            case 'assigned': $statusClass = 'status-assigned'; break;
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <?php if (strtolower($row['status']) !== 'completed'): ?>
                                    <a class="btns" href="assign_driver.php?id=<?php echo $row['id']; ?>">Assign / Complete</a>
                                <?php else: ?>
                                    <span>Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No reports found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
