<?php
if(isset($_POST['option'])){
    $option = $_POST['option'];

    if($option == 'রোগী'){
        header("Location: patient.php"); // Page with লগইন করুন / রেজিস্ট্রেশন করুন buttons
        exit();
    } elseif($option == 'ডাক্তার'){
        header("Location: doctor_login.php"); // Doctor login page
        exit();
    } elseif($option == 'হাসপাতাল'){
        // You can add hospital page later
        echo "<h2>হাসপাতাল পেজ এখনও তৈরি হয়নি</h2>";
    }
}
?>
