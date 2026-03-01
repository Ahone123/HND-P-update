<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCity | Waste Management</title>
    <!-- add version to force reload when changes made -->
    <link rel="stylesheet" href="style.css?v=2">
</head>

<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">CleanCity</div>
        <button class="menu-toggle" aria-label="Toggle navigation">☰</button>
        <nav>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="../Auth/register.php" class="btn login-btn btn-register">Register</a>
            <a href="../Auth/login.php" class="btn login-btn btn-user">User Login</a>
            <a href="../Auth/admin_login.php" class="btn login-btn btn-admin">Admin Login</a>
        </nav>
    </header>
    <script>
        // mobile menu toggle
        document.addEventListener('DOMContentLoaded', function(){
            const toggle = document.querySelector('.menu-toggle');
            const nav = document.querySelector('.navbar nav');
            console.log('menu toggle script loaded');
            toggle.addEventListener('click', () => {
                nav.classList.toggle('open');
            });
        });
    </script>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-text">
            <h1>Report Waste. Keep Your City Clean.</h1>
            <p>
                Quickly and easily report waste in your area.
                Together, we can make our community cleaner and safer.
            </p>

            <div class="hero-buttons">
                <a href="../User/report_waste.php" class="btn primary">Report Waste</a>
            </div>
        </div>

    </section>

    <!-- FEATURES -->
    <section class="features">
        <div class="card">
            <h3>⚡ Fast Reporting</h3>
            <p>Submit a report in seconds through our streamlined system.</p>
        </div>

        <div class="card">
            <h3>🛡 Verified Drivers</h3>
            <p>All collection drivers are vetted and verified.</p>
        </div>

        <div class="card">
            <h3>🗺 Transparent Tracking</h3>
            <p>Track the status of your report from submission to completion.</p>
        </div>
    </section>
    
    <!-- ABOUT SECTION -->
    <section class="about container">
        <h2>About the System</h2>
        <p>This platform helps residents report waste easily and ensures quick waste collection by
           connecting users, administrators and drivers.</p>

        <h3>Problem</h3>
        <p>Poor communication causes delayed waste collection and environmental pollution.</p>

        <h3>Solution</h3>
        <p>Residents upload waste images, admins assign drivers, and collection is confirmed with photos.</p>
    </section>

</html>