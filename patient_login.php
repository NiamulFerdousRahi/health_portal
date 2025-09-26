<?php
session_start();
include("db.php"); // DB connection

// যদি ইউজার ইতিমধ্যেই লগইন থাকে, সরাসরি dashboard-এ যাবে
if(isset($_SESSION['patient_id'])){
    header("Location: dashboard.php");
    exit();
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $emailOrMobile = $_POST['email']; // ইমেল বা মোবাইল
    $password = $_POST['password'];

    // ডাটাবেস থেকে মিল খুঁজবে
    $sql = "SELECT * FROM patients WHERE email=? OR mobile=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emailOrMobile, $emailOrMobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();

        // যদি ডাটাবেসে hashed password থাকে
        if(password_verify($password, $user['password'])){
            $_SESSION['patient_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email']; // Dashboard session এ প্রয়োজন
            header("Location: dashboard.php");
            exit();
        }
        // যদি ডাটাবেসে plain text password থাকে, এই লাইন ব্যবহার করো:
        // if($password === $user['password']){
        else {
            $error = "পাসওয়ার্ড সঠিক নয়!";
        }
    } else {
        $error = "ইউজার পাওয়া যায়নি!";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>লগইন করুন</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 20px; background: linear-gradient(135deg, #dff6f0, #d2dbdaff); margin:0; }
form { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); width: 100%; max-width: 400px;}
h2 { text-align:center; color:#34322bff; margin-bottom:20px; font-size:22px;}
label { display:block; margin-top:12px; text-align:left; font-weight:bold; color:#333; font-size:14px;}
input { width:100%; padding:10px; margin-top:6px; border:1px solid #ccc; border-radius:8px; font-size:15px; transition: all 0.2s ease; }
input:focus { border-color:#006a4e; outline:none; box-shadow:0 0 6px rgba(0,106,78,0.4); }
button { margin-top:20px; padding:12px; width:100%; background:#006a4e; color:white; border:none; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer; transition: background 0.3s ease, transform 0.2s ease; }
button:hover { background:#004d36; transform: scale(1.02); }
.error { color: red; margin-top:10px; text-align:center; font-weight:bold; }
@media(max-width:480px){ form{padding:20px;border-radius:12px;} h2{font-size:20px;} label,input,button{font-size:14px;padding:9px;} }
</style>
</head>
<body>
<form method="post">
<h2>লগইন করুন</h2>

<?php if(!empty($error)){ echo "<div class='error'>$error</div>"; } ?>

<label>ইমেল বা মোবাইল:</label>
<input type="text" name="email" required pattern="(^[0-9]{11}$)|(^[\w\.-]+@[\w\.-]+\.\w{2,4}$)" title="১১ সংখ্যার মোবাইল নম্বর অথবা বৈধ ইমেল দিন">

<label>পাসওয়ার্ড:</label>
<input type="password" name="password" required
pattern="^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$"
title="পাসওয়ার্ডে অন্তত ৮ অক্ষর, ১টি বড় হাতের অক্ষর, ১টি ছোট হাতের অক্ষর, ১টি সংখ্যা এবং ১টি বিশেষ চিহ্ন থাকতে হবে">

<button type="submit">লগইন</button>
</form>
</body>
</html>
