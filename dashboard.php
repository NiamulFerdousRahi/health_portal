<?php
session_start();
include('db.php'); // Include your DB connection

// Check if patient is logged in
if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch patient information
$patientQuery = "SELECT * FROM patients WHERE email='$email'";
$patient = $conn->query($patientQuery)->fetch_assoc();

// Handle PDF upload
if(isset($_POST['upload'])){
    $targetDir = "uploads/";
    if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = basename($_FILES["pdf"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

    if($fileType == "pdf"){
        if(move_uploaded_file($_FILES["pdf"]["tmp_name"], $targetFile)){
            $insert = "INSERT INTO prescriptions (patient_email, file_name) VALUES ('$email', '$targetFile')";
            $conn->query($insert);
            echo "<script>alert('Prescription uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Failed to upload file.');</script>";
        }
    } else {
        echo "<script>alert('Please upload PDF file only.');</script>";
    }
}

// Fetch prescriptions
$prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$email' ORDER BY upload_date DESC");

// Fetch doctor entries
$doctor_entries = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$email' ORDER BY entry_date DESC");
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>রোগীর ড্যাশবোর্ড</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; padding: 20px; }
.container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
h2 { color: #006a4e; text-align: center; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table, th, td { border: 1px solid #ccc; }
th, td { padding: 10px; text-align: left; }
input, button { padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px; }
button { background: #006a4e; color: white; border: none; cursor: pointer; }
button:hover { background: #004d36; }
form { margin-top: 20px; }
</style>
</head>
<body>
<div class="container">

<h2>রোগীর ড্যাশবোর্ড</h2>

<h3>ব্যক্তিগত তথ্য</h3>
<p><strong>নাম:</strong> <?php echo $patient['name']; ?></p>
<p><strong>জন্ম তারিখ:</strong> <?php echo $patient['dob']; ?></p>
<p><strong>লিঙ্গ:</strong> <?php echo $patient['gender']; ?></p>
<p><strong>জেলা:</strong> <?php echo $patient['district']; ?></p>
<p><strong>উপজেলা / থানা:</strong> <?php echo $patient['upazila']; ?></p>
<p><strong>মোবাইল:</strong> <?php echo $patient['mobile']; ?></p>
<p><strong>ইমেল:</strong> <?php echo $patient['email']; ?></p>

<h3>প্রিস্ক্রিপশন আপলোড করুন (PDF)</h3>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="pdf" required>
    <button type="submit" name="upload">Upload</button>
</form>

<h3>আপনার প্রিস্ক্রিপশন তালিকা</h3>
<table>
<tr>
<th>ফাইল</th>
<th>আপলোডের তারিখ</th>
<th>ডাউনলোড</th>
</tr>
<?php while($row = $prescriptions->fetch_assoc()){ ?>
<tr>
<td><?php echo basename($row['file_name']); ?></td>
<td><?php echo $row['upload_date']; ?></td>
<td><a href="<?php echo $row['file_name']; ?>" target="_blank">ডাউনলোড</a></td>
</tr>
<?php } ?>
</table>

<h3>ডাক্তারের পরামর্শ ও ঔষধ তালিকা</h3>
<table>
<tr>
<th>তারিখ</th>
<th>রোগের বিবরণ</th>
<th>ঔষধ</th>
<th>সেবন বিধি</th>
<th>দিন</th>
<th>পরামর্শ</th>
</tr>
<?php while($row = $doctor_entries->fetch_assoc()){ ?>
<tr>
<td><?php echo $row['entry_date']; ?></td>
<td><?php echo $row['disease_description']; ?></td>
<td><?php echo $row['medicine_name']; ?></td>
<td><?php echo $row['dosage']; ?></td>
<td><?php echo $row['days']; ?></td>
<td><?php echo $row['advice']; ?></td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
