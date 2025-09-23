<?php
session_start();
include('db.php');

if(!isset($_SESSION['doctor_email'])){
    header("Location: doctor_login.php");
    exit();
}

$patient_data = null;
$patient_email = '';
$prescriptions = null;
$latest_entry = null;

// Handle fetching patient
if(isset($_POST['patient_email']) && !isset($_POST['insert']) && !isset($_POST['update'])){
    $patient_email = $_POST['patient_email'];
    $query = "SELECT * FROM patients WHERE email='$patient_email'";
    $patient_data = $conn->query($query)->fetch_assoc();

    // Fetch patient prescriptions
    $prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$patient_email' ORDER BY upload_date DESC");

    // Fetch latest doctor entry
    $latest_entry = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
}

// Handle inserting new doctor entry
if(isset($_POST['insert'])){
    $patient_email = $_POST['patient_email'];
    $entry_date = $_POST['entry_date'];
    $disease_description = $_POST['disease_description'];
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $days = $_POST['days'];
    $advice = $_POST['advice'];

    $insert = "INSERT INTO doctor_entries (patient_email, entry_date, disease_description, medicine_name, dosage, days, advice) 
               VALUES ('$patient_email','$entry_date','$disease_description','$medicine_name','$dosage','$days','$advice')";
    if($conn->query($insert)){
        echo "<script>alert('নতুন ডেটা সফলভাবে সংরক্ষিত হয়েছে!');</script>";
    }

    // reload
    $patient_data = $conn->query("SELECT * FROM patients WHERE email='$patient_email'")->fetch_assoc();
    $prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$patient_email' ORDER BY upload_date DESC");
    $latest_entry = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
}

// Handle updating latest doctor entry
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

    // reload
    $patient_data = $conn->query("SELECT * FROM patients WHERE email='$patient_email'")->fetch_assoc();
    $prescriptions = $conn->query("SELECT * FROM prescriptions WHERE patient_email='$patient_email' ORDER BY upload_date DESC");
    $latest_entry = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC LIMIT 1")->fetch_assoc();
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
</style>
</head>
<body>
<div class="container">
<h2>ডাক্তার ড্যাশবোর্ড</h2>

<form method="post">
    <label>রোগীর ইমেল আইডি দিন:</label>
    <input type="email" name="patient_email" value="<?php echo $patient_email; ?>" required>
    <button type="submit">রোগী দেখুন</button>
</form>

<?php if($patient_data){ ?>
<hr>
<h3>রোগীর তথ্য: <?php echo $patient_data['name']; ?></h3>

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

<!-- Insert Form -->
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

<!-- Update latest entry -->
<h3>সর্বশেষ ডেটা সংশোধন করুন</h3>
<?php if($latest_entry){ ?>
<form method="post">
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
    <button type="submit" name="update">সংশোধন করুন</button>
</form>
<?php } else { ?>
<p>কোনো ডক্টর এন্ট্রি এখনো যোগ করা হয় নি।</p>
<?php } ?>

<h3>রোগীর পূর্ববর্তী সব ডেটা</h3>
<table>
<tr>
<th>তারিখ</th>
<th>রোগের বিবরণ</th>
<th>ঔষধ</th>
<th>সেবন বিধি</th>
<th>দিন</th>
<th>পরামর্শ</th>
</tr>
<?php
$entries = $conn->query("SELECT * FROM doctor_entries WHERE patient_email='$patient_email' ORDER BY entry_date DESC");
while($row = $entries->fetch_assoc()){
    echo "<tr>
        <td>".$row['entry_date']."</td>
        <td>".$row['disease_description']."</td>
        <td>".$row['medicine_name']."</td>
        <td>".$row['dosage']."</td>
        <td>".$row['days']."</td>
        <td>".$row['advice']."</td>
    </tr>";
}
?>
</table>
<?php } ?>
</div>
</body>
</html>