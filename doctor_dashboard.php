<?php
session_start();
include('db.php');

// যদি ডাক্তার লগইন না করা থাকে
if(!isset($_SESSION['doctor_email'])){
    header("Location: doctor_login.php");
    exit();
}

// ডাক্তারের session
$doctor_email = $_SESSION['doctor_email'];
$doctor_designation = $_SESSION['doctor_designation'];

// ডাটাবেস থেকে ডাক্তারের নাম
$doctor_query = $conn->query("SELECT name FROM doctors WHERE email='$doctor_email' LIMIT 1");
$doctor = $doctor_query->fetch_assoc();
$doctor_name = $doctor ? $doctor['name'] : '';

$patient_data = null;
$patient_email = '';
$prescriptions = null;
$latest_entry = null;

// রোগী fetch
if(isset($_POST['patient_email']) && !isset($_POST['insert']) && !isset($_POST['update'])){
    $patient_email = $_POST['patient_email'];
    $patient_data = $conn->query("SELECT * FROM patients WHERE email='$patient_email'")->fetch_assoc();
    $prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$patient_email' ORDER BY upload_date DESC");
    $latest_entry = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
}

// নতুন entry insert
if(isset($_POST['insert'])){
    $patient_email = $_POST['patient_email'];
    $entry_date = $_POST['entry_date'];
    $disease_description = $_POST['disease_description'];
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $days = $_POST['days'];
    $advice = $_POST['advice'];

    // doctor_info JSON encode
    $doctor_info = json_encode([
        'doctor_email' => $doctor_email,
        'doctor_name' => $doctor_name,
        'doctor_designation' => $doctor_designation
    ], JSON_UNESCAPED_UNICODE);

    $insert = "INSERT INTO doctor_entries 
               (patient_email, entry_date, disease_description, medicine_name, dosage, days, advice, doctor_info) 
               VALUES 
               ('$patient_email','$entry_date','$disease_description','$medicine_name','$dosage','$days','$advice','$doctor_info')";

    if($conn->query($insert)){
        echo "<script>alert('নতুন ডেটা সফলভাবে সংরক্ষিত হয়েছে!');</script>";
    }

    $patient_data = $conn->query("SELECT * FROM patients WHERE email='$patient_email'")->fetch_assoc();
    $prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$patient_email' ORDER BY upload_date DESC");
    $latest_entry = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
}

// latest entry update
if(isset($_POST['update'])){
    $entry_id = $_POST['entry_id'];
    $patient_email = $_POST['patient_email'];
    $entry_date = $_POST['entry_date'];
    $disease_description = $_POST['disease_description'];
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $days = $_POST['days'];
    $advice = $_POST['advice'];

    $update = "UPDATE doctor_entries 
               SET entry_date='$entry_date',
                   disease_description='$disease_description',
                   medicine_name='$medicine_name',
                   dosage='$dosage',
                   days='$days',
                   advice='$advice'
               WHERE id='$entry_id'";
    if($conn->query($update)){
        echo "<script>alert('সর্বশেষ ডেটা সফলভাবে সংশোধন করা হয়েছে!');</script>";
    }

    $patient_data = $conn->query("SELECT * FROM patients WHERE email='$patient_email'")->fetch_assoc();
    $prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$patient_email' ORDER BY upload_date DESC");
    $latest_entry = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
}

// রোগীর বয়স হিসাব
function calculate_age($dob){
    if(!$dob) return '';
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>ডাক্তার ড্যাশবোর্ড</title>
<style>
body { font-family: "Noto Sans Bengali", sans-serif; background: #f5faff; padding: 20px; }
.container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
input, textarea { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ccc; border-radius: 6px; }
button { background: #006a4e; color: white; padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; margin-top: 10px; }
button:hover { background: #004d36; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table, th, td { border: 1px solid #ccc; }
th, td { padding: 10px; text-align: left; }
.header { background: #006a4e; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center; }
</style>
<script>
function toggleUpdateForm() {
    var form = document.getElementById('updateForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>
</head>
<body>
<div class="container">

<div class="header">
    <div>
        <h2>ডাক্তার ড্যাশবোর্ড</h2>
        <p>স্বাগতম, <b><?php echo $doctor_name; ?></b> (<?php echo $doctor_designation; ?>)</p>
    </div>
    <div>
        <form method="post" action="index.html">
            <button type="submit">লগআউট</button>
        </form>
    </div>
</div>

<form method="post">
    <label>রোগীর ইমেল আইডি দিন:</label>
    <input type="email" name="patient_email" value="<?php echo $patient_email; ?>" required>
    <button type="submit">রোগী দেখুন</button>
</form>

<?php if($patient_data){ ?>
<hr>
<h3>রোগীর তথ্য: <?php echo $patient_data['name']; ?></h3>
<p>বয়স: <?php echo calculate_age($patient_data['dob']); ?> বছর</p>
<p>লিঙ্গ: <?php echo $patient_data['gender']; ?></p>

<h3>রোগীর আপলোডকৃত প্রিস্ক্রিপশন</h3>
<?php if($prescriptions && $prescriptions->num_rows > 0){ ?>
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
<?php } else { ?>
<p>রোগীর কোনো প্রিস্ক্রিপশন আপলোড করা হয় নি।</p>
<?php } ?>

<h3>নতুন ডক্টর এন্ট্রি যোগ করুন</h3>
<form method="post">
    <input type="hidden" name="patient_email" value="<?php echo $patient_email; ?>">
    <label>তারিখ:</label>
    <input type="date" name="entry_date" required>
    <label>রোগের বিবরণ:</label>
    <textarea name="disease_description" rows="3" required></textarea>
    <label>ঔষধের নাম:</label>
    <input type="text" name="medicine_name" required>
    <label>সেবন বিধি:</label>
    <input type="text" name="dosage" required>
    <label>দিন:</label>
    <input type="text" name="days" required>
    <label>পরামর্শ:</label>
    <textarea name="advice" rows="2" required></textarea>
    <button type="submit" name="insert">সংরক্ষণ করুন</button>
</form>

<h3>রোগীর পূর্ববর্তী সব ডেটা</h3>
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
<?php
$entries = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC");
while($row = $entries->fetch_assoc()){
    $doctor_info = json_decode($row['doctor_info'], true);
    echo "<tr>
        <td>".$row['entry_date']."</td>
        <td>".$row['disease_description']."</td>
        <td>".$row['medicine_name']."</td>
        <td>".$row['dosage']."</td>
        <td>".$row['days']."</td>
        <td>".$row['advice']."</td>
        <td>
            ডাক্তারঃ ".($doctor_info['doctor_name'] ?? '')."<br>
            পদবীঃ ".($doctor_info['doctor_designation'] ?? '')."<br>
            জরুরী প্রয়োজনঃ ".($doctor_info['doctor_email'] ?? '')."
        </td>
    </tr>";
}
?>
</table>

<h3>সর্বশেষ ডেটা সংশোধন করুন</h3>
<?php if($latest_entry){ ?>
<button type="button" onclick="toggleUpdateForm()">সর্বশেষ ডেটা সংশোধন করুন</button>
<form method="post" id="updateForm" style="display:none; margin-top:20px;">
    <input type="hidden" name="entry_id" value="<?php echo $latest_entry['id']; ?>">
    <input type="hidden" name="patient_email" value="<?php echo $patient_email; ?>">
    <label>তারিখ:</label>
    <input type="date" name="entry_date" value="<?php echo $latest_entry['entry_date']; ?>" required>
    <label>রোগের বিবরণ:</label>
    <textarea name="disease_description" rows="3" required><?php echo $latest_entry['disease_description']; ?></textarea>
    <label>ঔষধের নাম:</label>
    <input type="text" name="medicine_name" value="<?php echo $latest_entry['medicine_name']; ?>" required>
    <label>সেবন বিধি:</label>
    <input type="text" name="dosage" value="<?php echo $latest_entry['dosage']; ?>" required>
    <label>দিন:</label>
    <input type="text" name="days" value="<?php echo $latest_entry['days']; ?>" required>
    <label>পরামর্শ:</label>
    <textarea name="advice" rows="2" required><?php echo $latest_entry['advice']; ?></textarea>
    <button type="submit" name="update">সংশোধন সম্পন্ন করুন</button>
</form>
<?php } else { ?>
<p>কোনো ডক্টর এন্ট্রি এখনো যোগ করা হয় নি।</p>
<?php } ?>

<?php } ?>
</div>
</body>
</html>