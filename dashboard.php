<?php
session_start();
include('db.php'); // DB connection

// Check if patient is logged in
if(!isset($_SESSION['patient_id'])){
    header("Location: patient_login.php");
    exit();
}

// Use session safely
$patient_id = $_SESSION['patient_id'];
$email = $_SESSION['email'] ?? '';

// Fetch patient information safely
$patientQuery = "SELECT * FROM patients WHERE id=?";
$stmt = $conn->prepare($patientQuery);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if(!$patient){
    echo "Patient data not found.";
    exit();
}

// Handle logout
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.html");
    exit();
}

// Handle PDF upload
if(isset($_POST['upload'])){
    $targetDir = "uploads/";
    if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = basename($_FILES["pdf"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

    if($fileType == "pdf"){
        if(move_uploaded_file($_FILES["pdf"]["tmp_name"], $targetFile)){
            $insert = "INSERT INTO prescriptions (patient_email, file_name) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($insert);
            $stmtInsert->bind_param("ss", $email, $targetFile);
            $stmtInsert->execute();
            echo "<script>alert('Prescription uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Failed to upload file.');</script>";
        }
    } else {
        echo "<script>alert('Please upload PDF file only.');</script>";
    }
}

// Fetch prescriptions safely
$stmtPresc = $conn->prepare("SELECT * FROM prescriptions WHERE patient_email=? ORDER BY upload_date DESC");
$stmtPresc->bind_param("s", $email);
$stmtPresc->execute();
$prescriptions = $stmtPresc->get_result();

// Fetch doctor entries safely
$stmtDoctor = $conn->prepare("SELECT * FROM doctor_entries WHERE patient_email=? ORDER BY entry_date DESC");
$stmtDoctor->bind_param("s", $email);
$stmtDoctor->execute();
$doctor_entries = $stmtDoctor->get_result();

function calculate_age($dob){
    $birthdate = new DateTime($dob);
    $today = new DateTime();
    return $today->diff($birthdate)->y;
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>রোগীর ড্যাশবোর্ড</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; padding: 20px; }
.container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
h2 { color: #006a4e; text-align: center; margin: 0; }
.logout-btn {
    float: right;
    background: #e63946;
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
}
.logout-btn:hover { background: #b71c1c; }
.header { overflow: hidden; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table, th, td { border: 1px solid #ccc; }
th, td { padding: 10px; text-align: left; vertical-align: top; }
input, button { padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px; }
button { background: #006a4e; color: white; border: none; cursor: pointer; }
button:hover { background: #004d36; }
form { margin-top: 20px; }
</style>
</head>
<body>
<div class="container">

<div class="header">
    <h2>রোগীর ড্যাশবোর্ড</h2>
    <a href="?logout=true" class="logout-btn">লগআউট</a>
</div>

<h3>ব্যক্তিগত তথ্য</h3>
<p><strong>নাম:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
<p><strong>বয়স:</strong> <?php echo calculate_age($patient['dob']); ?> বছর</p>
<p><strong>জন্ম তারিখ:</strong> <?php echo htmlspecialchars($patient['dob']); ?></p>
<p><strong>লিঙ্গ:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
<p><strong>জেলা:</strong> <?php echo htmlspecialchars($patient['district']); ?></p>
<p><strong>উপজেলা / থানা:</strong> <?php echo htmlspecialchars($patient['upazila']); ?></p>
<p><strong>মোবাইল:</strong> <?php echo htmlspecialchars($patient['mobile']); ?></p>
<p><strong>ইমেল:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>

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
<td><?php echo htmlspecialchars(basename($row['file_name'])); ?></td>
<td><?php echo htmlspecialchars($row['upload_date']); ?></td>
<td><a href="<?php echo htmlspecialchars($row['file_name']); ?>" target="_blank">ডাউনলোড</a></td>
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
<th>ডাক্তারের তথ্য</th>
</tr>
<?php while($row = $doctor_entries->fetch_assoc()){ 
    $doctor_info = json_decode($row['doctor_info'], true);
?>
<tr>
<td><?php echo htmlspecialchars($row['entry_date']); ?></td>
<td><?php echo htmlspecialchars($row['disease_description']); ?></td>
<td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
<td><?php echo htmlspecialchars($row['dosage']); ?></td>
<td><?php echo htmlspecialchars($row['days']); ?></td>
<td><?php echo htmlspecialchars($row['advice']); ?></td>
<td>
<?php if($doctor_info){ ?>
ডাক্তারঃ <?php echo htmlspecialchars($doctor_info['doctor_name']); ?><br>
পদবীঃ <?php echo htmlspecialchars($doctor_info['doctor_designation']); ?><br>
জরুরী প্রয়োজনঃ <?php echo htmlspecialchars($doctor_info['doctor_email']); ?>
<?php } ?>
</td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
