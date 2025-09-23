<?php
session_start();
include('db.php');

if(isset($_POST['email'])){
    $email = $_POST['email'];

    // Check if email ends with '@dghs.gov.bd'
    if(substr($email, -12) !== '@dghs.gov.bd'){
        echo "<script>alert('ডাক্তারের ইমেল ঠিকানা সঠিক নয়!');</script>";
    } else {
        $_SESSION['doctor_email'] = $email;
        header("Location: doctor_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>ডাক্তার লগইন</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f5faff; }
form { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
input { width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 6px; }
button { margin-top: 20px; padding: 10px; width: 100%; background: #006a4e; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
button:hover { background: #004d36; }
</style>
</head>
<body>
<form method="post">
<h2 style="text-align:center;">ডাক্তার লগইন</h2>
<label>ইমেল ঠিকানা:</label>
<input type="email" name="email" required>
<button type="submit">লগইন</button>
</form>
</body>
</html>