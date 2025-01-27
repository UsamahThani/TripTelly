<?php
session_start();
include 'dbconnect.php';

require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    // Generate a unique token
    $token = bin2hex(random_bytes(50));

    $sql = "SELECT * FROM user WHERE userEmail = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['errorMsg'] = "Email not found!";
        header("Location: resetPassword.php");
    }

    // Generate a temporary password
    $temporaryPassword = bin2hex(random_bytes(4)); // Generates an 8-character string
    $hashedPassword = password_hash($temporaryPassword, PASSWORD_DEFAULT);

    // Update the user's password in the database
    $conn->query("UPDATE user SET userPassword = '$hashedPassword' WHERE userEmail = '$email'");

    // Store the token and email in the password_reset table
    $stmt = $conn->prepare("INSERT INTO password_reset (email, token) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->close();

    // Send the email
    // Configure PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // need new gmail here and genereate app password
        $mail->Username = 'erfatriptelly@gmail.com';
        $mail->Password = 'xaxg dbfx xjjc mlbm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('no-reply@triptelly.com', 'TripTelly');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = 'Click the link to reset your password: <a href="http://localhost/triptelly/formResetPassword.php?token=' . $token . '">Reset Password</a>';

        $mail->send();
        $_SESSION['successMsg'] = "Please check your email.";
    } catch (Exception $e) {
        $_SESSION['errorMsg'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    header("Location: signin.php");

    $conn->close();
    exit();
}

?>