<?php
require_once "../includes/database.php";
require_once "../includes/functions.php";
session_start();

if (!isset($_SESSION['pending_email'])) {
    redirect("./signup.php");
}

$email = $_SESSION['pending_email'];

// Generate new OTP
$otp = rand(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

$stmt = $pdo->prepare("UPDATE customers SET otp_code = :otp, otp_expiry = :expiry WHERE email = :email");
$stmt->execute([
    ":otp" => $otp,
    ":expiry" => $otp_expiry,
    ":email" => $email
]);

// Send OTP again
sendEmailOTP($email, $otp);

// Redirect back to verify page
redirect("./otp_verify.php?resend=success");
