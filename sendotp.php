<?php
session_start();
include('smtp/PHPMailerAutoload.php');

if (isset($_POST['email'])) {
    // Generate OTP
    $otp = rand(100000, 999999); //অউ রেঞ্জের মধ্যে। ৬ সংখ্যার।
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
    $subject = "স্বাস্থ্য বাতায়ন এ রেজিস্ট্রেশন করার জন্য OTP";
    $emailbody = "আপনার ৬ অঙ্কের OTP কোড: $otp";

    if (smtp_mailer($receiverEmail, $subject, $emailbody)) {
        echo "<script>alert('OTP আপনার ইমেলে পাঠানো হয়েছে।'); window.location.href='verify_otp.php';</script>";
        exit();
    } else {
        echo "<script>alert('OTP পাঠানো যায়নি। আবার চেষ্টা করুন।'); window.location.href='register.php';</script>";
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
    $mail->Username = "mnfrahi2025@gmail.com"; //যে মেইল তাকি পাঠাবো
    $mail->Password = "kcke wnst eyta kdyr"; // Gmail App password
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