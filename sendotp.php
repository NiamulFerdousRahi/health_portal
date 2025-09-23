<?php
session_start();
include('smtp/PHPMailerAutoload.php');

if (isset($_POST['email'])) {
    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    // Save registration data in session
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['dob'] = $_POST['dob'];
    $_SESSION['gender'] = $_POST['gender'];
    $_SESSION['district'] = $_POST['district'];
    $_SESSION['upazila'] = $_POST['upazila'];
    $_SESSION['mobile'] = $_POST['mobile'];
    $_SESSION['email'] = $_POST['email'];

    $receiverEmail = $_POST['email'];
    $subject = "Email Verification";
    $emailbody = "Your 6 Digit OTP Code: $otp";

    if (smtp_mailer($receiverEmail, $subject, $emailbody)) {
        echo "<script>alert('OTP has been sent to your email.'); window.location.href='verify_otp.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to send OTP. Try again.'); window.location.href='register.php';</script>";
    }
}

// SMTP Mailer function
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "mnfrahi2025@gmail.com"; // Change this
    $mail->Password = "kcke wnst eyta kdyr";   // Change this (App password)
    $mail->SetFrom("mnfrahi2025@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    return $mail->Send();
}
?>
