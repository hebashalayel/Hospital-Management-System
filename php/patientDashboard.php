<?php
session_start();
require '../DB/config.php';
//make sure login as patient to show patientdashboard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: loginForm.php");
    exit();
}
// select patient data to show in patient profile
$patient_id = $_SESSION['user_id'];
$patient_stmt = $con->prepare("SELECT * FROM Patients WHERE id = ?");
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_data = $patient_stmt->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="en">
<head>
    <title>Patient Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/patientDashboard.css">
</head>
<body>
    <header>
        <!-- greeting message -->
        <h1>Welcome, <?= htmlspecialchars($patient_data['name']); ?></h1>
        <button onclick="window.location.href='logout.php';">Logout</button>
    </header>
    <main>
        <section class="profile-section">
            <h2><i class="fa-solid fa-user"></i> Your Profile</h2>
             <!-- show patient information -->
            <p><strong>Name:</strong> <?= htmlspecialchars($patient_data['name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($patient_data['email']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($patient_data['phoneNumber']); ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($patient_data['gender']); ?></p>
            <!-- open the same page update_patient that doctor can also update the patient data from it -->
            <button onclick="window.location.href='update_patient.php?id=<?= $patient_id; ?>';"> Update Profile</button>    
        </section>
    </main>
</body>
</html>
