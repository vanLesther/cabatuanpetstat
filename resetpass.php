<?php
require_once("class/db_connect.php");
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// reset_password_request.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    global $conn;

    // Check if the email exists in the database
    $query = "SELECT * FROM resident WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));

        // Store token in the database
        $query1 = "UPDATE resident SET reset_token = '$token' WHERE email = '$email'";
        mysqli_query($conn, $query1);

        // Send email with reset link using PHPMailer
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.porkbun.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@capstone-petstat.wiki';
        $mail->Password = 'Asadawe123';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('admin@capstone-petstat.wiki', 'PetStat Admin');
        $mail->addAddress($email);

        $mail->Subject = 'Password Reset';
        $mail->Body = "Hi!,<br><br>
                There was a request to change your password!<br>
                If you did not make this request, please ignore this email.<br><br>
                Otherwise, please click this link to change your password: <a href='http://localhost:8080/petstatfinal1/reset_password.php?token=$token'>Reset Password</a><br><br>
        
        Yours,<br><br>
        The Petstat team";
    
        $mail->isHTML(true);  // Set content type to HTML

        if ($mail->send()) {
            echo "Password reset instructions sent to your email.";
        } else {
            echo "Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        echo "Email not found in our records.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <h2>Forgot Password</h2>
    <form action="resetpass.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
