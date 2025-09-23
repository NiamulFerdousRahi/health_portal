<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <title>রেজিস্ট্রেশন ফর্ম</title>
  <style>
    body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; display: flex; justify-content: center; align-items: center; height: 100vh; }
    form {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 350px;
    }
    label { display: block; margin-top: 10px; text-align: left; }
    input, select {
      width: 100%; padding: 8px; margin-top: 5px;
      border: 1px solid #ccc; border-radius: 6px;
    }
    button {
      margin-top: 20px; padding: 10px; width: 100%;
      background: #006a4e; color: white; border: none; border-radius: 8px; font-size: 16px;
    }
    button:hover { background: #004d36; }
  </style>
</head>
<body>
  <form action="sendotp.php" method="post">
    <h2 style="text-align:center;">রেজিস্ট্রেশন</h2>
    <label>নাম:</label><input type="text" name="name" required>
    <label>জন্ম তারিখ:</label><input type="date" name="dob" required>
    <label>লিঙ্গ:</label>
    <select name="gender" required>
      <option value="পুরুষ">পুরুষ</option>
      <option value="মহিলা">মহিলা</option>
      <option value="অন্যান্য">অন্যান্য</option>
    </select>
    <label>জেলা:</label><input type="text" name="district" required>
    <label>উপজেলা / থানা:</label><input type="text" name="upazila" required>
    <label>মোবাইল নম্বর:</label><input type="text" name="mobile" required>
    <label>ইমেল ঠিকানা:</label><input type="email" name="email" required>
    <button type="submit">OTP পাঠান</button>
  </form>
</body>
</html>
