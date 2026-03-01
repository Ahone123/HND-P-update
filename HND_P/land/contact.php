<?php
// contact.php - displays a contact form and saves submissions to the database
require_once __DIR__ . '/../config/db.php';

// ensure a table exists (run once). Example structure:
// CREATE TABLE contacts (
//   id INT AUTO_INCREMENT PRIMARY KEY,
//   name VARCHAR(100) NOT NULL,
//   email VARCHAR(150) NOT NULL,
//   subject VARCHAR(200) NOT NULL,
//   message TEXT,
//   created_at DATETIME DEFAULT CURRENT_TIMESTAMP
// );

$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $subject = $conn->real_escape_string(trim($_POST['subject']));
    $message = $conn->real_escape_string(trim($_POST['message']));

    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    if ($conn->query($sql)) {
        $feedback = '<p class="success">Thank you! Your message has been received.</p>';
    } else {
        $feedback = '<p class="error">Sorry, an error occurred. Please try again later.</p>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Contact</title>
<link rel="stylesheet" href="contact.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <h1>Get in Touch</h1>
        <p>We're here to help. Fill out the form below or use the contact information to reach us.</p>
    </div>
</section>

<section class="section container">
    <h2>Contact Us</h2>

    <p>Email: support@cleancity.com</p>
    <p>Phone: +237 674 30 79 67</p>

    <br>

    <?php echo $feedback; ?>
    <form method="post" action="contact.php">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="message" rows="5" placeholder="Message"></textarea>
        <button class="btn" type="submit">Send Message</button>
    </form>
</section>

<footer>
    © 2025 Waste Collection System
</footer>

</body>
</html>
</body>
</html>
