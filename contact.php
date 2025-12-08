<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect POST data safely
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Basic validation
if(!$name || !$email || !$message){
    die("Please fill in all required fields.");
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    die("Invalid email format.");
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();
$stmt->close();

// Send email
$mail = new PHPMailer(true);

try {
    // Enable verbose debug output (optional)
    // $mail->SMTPDebug = 2;

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'jonchetto12@gmail.com'; // Your Gmail
    $mail->Password   = 'gwtqattgooxyshqm';       // App password WITHOUT spaces
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls'
    $mail->Port       = 587;

    $mail->setFrom('jonchetto12@gmail.com', 'Portfolio Website');
    $mail->addAddress('jonchetto12@gmail.com'); // Where you want to receive messages

    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    = "
        <h3>New Contact Submission</h3>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Message:</strong> {$message}</p>
    ";

    $mail->send();
    echo 'Message sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

$conn->close();
?>
