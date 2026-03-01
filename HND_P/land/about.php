<?php
// about.php – informational page with simple PHP enhancements
require_once __DIR__ . '/../config/db.php'; // not strictly needed but loads configuration
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="about.css">
</head>

<body>

<nav>
    <h1>cleancity</h1>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="../Auth/login.php">Login</a></li>
    </ul>
</nav>

<section class="hero">
    <div class="hero-text">
        <h2>Our Mission</h2>
        <p>Learn how CleanCity connects people to faster, smarter waste reporting and collection.</p>
    </div>
</section>

<section class="section container">
    <h2>About the System</h2>

    <p>
    This platform helps residents report waste easily and ensures quick waste collection
    by connecting users, administrators and drivers.
    </p>

    <h3>Problem</h3>
    <p>
    Poor communication causes delayed waste collection and environmental pollution.
    </p>

    <h3>Solution</h3>
    <p>
    Residents upload waste images, admins assign drivers, and collection is confirmed with photos.
    </p>
</section>

<footer>
    © <?= $currentYear ?> Waste Collection System
</footer>

</body>
</html>
