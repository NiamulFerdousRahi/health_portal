<?php
session_start();
include("db.php"); // XAMPP DB connection

// যদি session-এ email না থাকে, register.php-এ redirect করবে
if(!isset($_SESSION['email'])){
    header("Location: register.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $name     = $_POST['name'];
    $dob      = $_POST['dob'];
    $gender   = $_POST['gender'];
    $district = $_POST['district'];
    $upazila  = $_POST['upazila'];
    $mobile   = $_POST['mobile'];
    $email    = $_SESSION['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password,PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO patients (name,dob,gender,district,upazila,mobile,email,password) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssss",$name,$dob,$gender,$district,$upazila,$mobile,$email,$hashed_password);

    if($stmt->execute()){
        session_destroy();
        echo "<script>alert('Registration Complete!'); window.location.href='patient.html';</script>";
        exit();
    } else {
        echo "<script>alert('Database Error!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>রেজিস্ট্রেশন সম্পন্ন করুন</title>
<style>
body { font-family:"Noto Sans Bengali",sans-serif; background:#f5faff; display:flex; justify-content:center; align-items:flex-start; min-height:100vh; margin:0; padding:20px;}
form { background:white; padding:25px; border-radius:16px; box-shadow:0 6px 20px rgba(0,0,0,0.15); width:100%; max-width:400px; }
input, select, button { width:100%; padding:10px; margin-top:10px; border-radius:8px; border:1px solid #ccc; font-size:15px; }
button { background:#006a4e; color:white; border:none; cursor:pointer; }
button:hover { background:#004d36; }
</style>
</head>
<body>
<form method="post">
<h2>রেজিস্ট্রেশন সম্পন্ন করুন</h2>

<label>নাম:</label>
<input type="text" name="name" required pattern="^[\u0980-\u09FF\s]+$">

<label>জন্ম তারিখ:</label>
<input type="date" name="dob" required>

<label>লিঙ্গ:</label>
<select name="gender" required>
<option value="" disabled selected>নির্বাচন করুন</option>
<option value="পুরুষ">পুরুষ</option>
<option value="মহিলা">মহিলা</option>
</select>

<label>জেলা:</label>
<input type="text" name="district" required pattern="^[\u0980-\u09FF\s]+$">

<label>উপজেলা / থানা:</label>
<input type="text" name="upazila" required pattern="^[\u0980-\u09FF\s]+$">

<label>মোবাইল:</label>
<input type="text" name="mobile" required pattern="^[\u0980-\u09FF\s]+$" title="১১ সংখ্যার মোবাইল নম্বর">

<label>পাসওয়ার্ড দিন:</label>
<input type="password" name="password" required
pattern="^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$"
title="পাসওয়ার্ডে অন্তত ৮ অক্ষর, ১টি বড় হাতের অক্ষর, ১টি ছোট হাতের অক্ষর, ১টি সংখ্যা এবং ১টি বিশেষ চিহ্ন থাকতে হবে">

<button type="submit">রেজিস্ট্রেশন সম্পন্ন করুন</button>
</form>
</body>
</html>
