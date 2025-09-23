<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['login'])) {
    $designation = $_POST['designation'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // MD5 ব্যবহার করা হয়েছে (demo)

    $sql = "SELECT * FROM doctors WHERE designation='$designation' AND email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['doctor_email'] = $email;
        $_SESSION['doctor_designation'] = $designation;
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        $error = "ভুল পদবী, ইমেইল বা পাসওয়ার্ড!";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>ডাক্তার লগইন</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; background: #eef6f9; padding: 50px; }
.container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
select, input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 6px; }
button { background: #006a4e; color: white; padding: 10px; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
button:hover { background: #004d36; }
.error { color: red; text-align: center; }
</style>
</head>
<body>
<div class="container">
    <h2 style="text-align:center;">ডাক্তার লগইন</h2>
    <?php if($error){ echo "<p class='error'>$error</p>"; } ?>
    <form method="post">
        <label>পদবী নির্বাচন করুন</label>
        <select name="designation" required>
            <option value="">-- নির্বাচন করুন --</option>
            <option value="আবাসিক মেডিকেল অফিসার">আবাসিক মেডিকেল অফিসার</option>
            <option value="মেডিকেল অফিসার">মেডিকেল অফিসার</option>
            <option value="জুনিয়র কনসালটেন্ট">জুনিয়র কনসালটেন্ট</option>
            <option value="সিনিয়র কনসালটেন্ট">সিনিয়র কনসালটেন্ট</option>
        </select>

        <label>ইমেইল</label>
        <input type="email" name="email" required>

        <label>পাসওয়ার্ড</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">লগইন করুন</button>
    </form>
</div>
</body>
</html>
