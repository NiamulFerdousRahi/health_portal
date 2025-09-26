<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: register.php");
    exit();
}

if(isset($_POST['otp'])){
    if($_POST['otp'] == $_SESSION['otp']){
        // OTP verified, go to final registration
        header("Location: final_register.php");
        exit();
    } else {
        echo "<script>alert('OTP মিলছে না! আবার চেষ্টা করুন।');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>OTP যাচাই</title>
<style>
body { font-family:"Noto Sans Bengali",sans-serif; background:#f5faff; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
form { background:white; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1); width:350px; }
input, button { width:100%; padding:10px; margin-top:10px; border-radius:6px; border:1px solid #ccc; font-size:15px; }
button { background:#006a4e; color:white; border:none; cursor:pointer; }
button:hover { background:#004d36; }
</style>
</head>
<body>
<form method="post">
  <h2>OTP যাচাই করুন</h2>
  <label>OTP:</label>
  <input type="text" name="otp" required placeholder="৬ অঙ্কের OTP লিখুন">
  <button type="submit">যাচাই করুন</button>
</form>
</body>
</html>
