<?php
session_start();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>রেজিস্ট্রেশন শুরু করুন</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
form { background:white; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1); width:350px; }
input, button { width:100%; padding:10px; margin-top:10px; border-radius:6px; border:1px solid #ccc; font-size:15px; }
button { background:#006a4e; color:white; border:none; cursor:pointer; }
button:hover { background:#004d36; }
</style>
</head>
<body>
<form method="post" action="sendotp.php">
  <h2>রেজিস্ট্রেশন শুরু করুন</h2>
  <label>ইমেল ঠিকানা:</label>
  <input type="email" name="email" required placeholder="আপনার ইমেল লিখুন">
  <button type="submit">OTP পাঠান</button>
</form>
</body>
</html>