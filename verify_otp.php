<?php
session_start();
include('db.php'); // Make sure this file contains your DB connection

if (isset($_POST['otp'])) {

    if (isset($_SESSION['otp']) && $_POST['otp'] == $_SESSION['otp']) {
        
        // Save data to database
        $name     = $_SESSION['name'];
        $dob      = $_SESSION['dob'];
        $gender   = $_SESSION['gender'];
        $district = $_SESSION['district'];
        $upazila  = $_SESSION['upazila'];
        $mobile   = $_SESSION['mobile'];
        $email    = $_SESSION['email'];

        $sql = "INSERT INTO patients (name, dob, gender, district, upazila, mobile, email) 
                VALUES ('$name', '$dob', '$gender', '$district', '$upazila', '$mobile', '$email')";

        if ($conn->query($sql) === TRUE) {
            // OTP Verified & data inserted successfully
            ?>
            <!DOCTYPE html>
            <html lang="bn">
            <head>
                <meta charset="UTF-8">
                <title>রেজিস্ট্রেশন সম্পন্ন</title>
                <style>
                    body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; text-align: center; padding: 50px; }
                    .card {
                        background: white; padding: 20px; border-radius: 12px;
                        box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; margin: auto;
                    }
                    h2 { color: #006a4e; }
                    p { text-align: left; margin: 8px 0; }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>✅ রেজিস্ট্রেশন সম্পন্ন হয়েছে!</h2>
                    <p><strong>নাম:</strong> <?php echo $name; ?></p>
                    <p><strong>জন্ম তারিখ:</strong> <?php echo $dob; ?></p>
                    <p><strong>লিঙ্গ:</strong> <?php echo $gender; ?></p>
                    <p><strong>জেলা:</strong> <?php echo $district; ?></p>
                    <p><strong>উপজেলা / থানা:</strong> <?php echo $upazila; ?></p>
                    <p><strong>মোবাইল নম্বর:</strong> <?php echo $mobile; ?></p>
                    <p><strong>ইমেল ঠিকানা:</strong> <?php echo $email; ?></p>
                </div>
            </body>
            </html>
            <?php
        } else {
            echo "Database Error: " . $conn->error;
        }

        // Clear session
        session_destroy();
        exit();

    } else {
        // Invalid OTP
        echo "<script>alert('Invalid OTP! Try Again.'); window.location.href='verify_otp.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>OTP যাচাই</title>
    <style>
        body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form {
            background: white; padding: 30px; border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px;
        }
        label { display: block; margin-top: 10px; text-align: left; }
        input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; }
        button {
            margin-top: 20px; padding: 10px; width: 100%;
            background: #006a4e; color: white; border: none; border-radius: 8px; font-size: 16px;
        }
        button:hover { background: #004d36; }
    </style>
</head>
<body>
    <form method="post">
        <h2 style="text-align:center;">OTP যাচাই করুন</h2>
        <label for="otp">OTP দিন:</label>
        <input type="text" name="otp" required>
        <button type="submit">যাচাই করুন</button>
    </form>
</body>
</html>
