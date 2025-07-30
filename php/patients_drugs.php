<?php
session_start();
require '../DB/config.php';

// Only login doctor can show patients with assigned drugs 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: loginForm.php");
    exit();
}

// store doctor id from session to select patients for this doctor
$doctor_id = $_SESSION['doctor_id'];

// Select patients assigned to the login doctor
$patients_stmt = $con->prepare("SELECT * FROM Patients WHERE id IN (SELECT pat_id FROM patientdoctor WHERE doc_id = ?)");
$patients_stmt->bind_param("i", $doctor_id);
$patients_stmt->execute();
$all_patients = $patients_stmt->get_result();

// Select all drugs 
$drugs_stmt = $con->prepare("SELECT * FROM Drugs");
$drugs_stmt->execute();
$all_drugs = $drugs_stmt->get_result();

// Handle form submission for filtter
$filter_patient_id = isset($_POST['patient_id']) ? $_POST['patient_id'] : null;
$filter_drug_id = isset($_POST['drug_id']) ? $_POST['drug_id'] : null;

// Select patient name and drug name and dosage according to selected drug and patient 
$filtter_query = "
    SELECT pd.*, p.name AS patient_name, d.name AS drug_name, d.dosage
    FROM patientDrug pd 
    JOIN Patients p ON pd.pat_id = p.id 
    JOIN Drugs d ON pd.drug_id = d.id 
    WHERE pd.pat_id IN (SELECT pat_id FROM patientdoctor WHERE doc_id = ?)";

$params = [$doctor_id]; 
$types = "i";
//if the user select patient select data with add where according to pat_id
if ($filter_patient_id) {
    $filtter_query .= " AND pd.pat_id = ?";
    $params[] = $filter_patient_id;
    $types .= "i";
}
//if the user select drug select data with add where according to drug_id
if ($filter_drug_id) {
    $filtter_query .= " AND pd.drug_id = ?";
    $params[] = $filter_drug_id;
    $types .= "i";
}

$drugs_stmt = $con->prepare($filtter_query);    
if ($drugs_stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($con->error)); 
}

if ($params) {
    $drugs_stmt->bind_param($types,$params);
}

$drugs_stmt->execute();
$drugs_result = $drugs_stmt->get_result();

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // if try to apply filtter without select any of patient or drug
    if (empty($_POST['patient_id']) && empty($_POST['drug_id'])) {
        $message = "Please select a filter before applying.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Show Patients Drugs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/patients_drugs.css">
</head>
<body>
    <div class="container">
        <h1><i class="fa-solid fa-capsules"></i> Patients Drugs</h1>
        <form class="filter-form" method="POST" action="">
            <?php if ($message): ?>
                <div id="message" class="alert alert-info">
                    <span id="message-text"><?= htmlspecialchars($message) ?></span>
                    <button id="close-message" class="close-btn">&times;</button>
                    <div class="loading-line"></div>
                </div>
            <?php endif; ?>
            <select name="patient_id">
                <option value="">Select Patient</option>
                <?php while ($patient = $all_patients->fetch_assoc()): ?>
                    <option value="<?= $patient['id'] ?>" <?= $filter_patient_id == $patient['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($patient['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="drug_id">
                <option value="">Select Drug</option>
                <?php while ($drug = $all_drugs->fetch_assoc()): ?>
                    <option value="<?= $drug['id'] ?>" <?= $filter_drug_id == $drug['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($drug['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit"><i class="fas fa-filter"></i> Apply Filter</button>
            <button type="button" onclick="window.location.href='<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>';" class="reset-button">
                <i class="fas fa-times"></i> Reset Filter
            </button>
        </form>
        <?php if ($drugs_result->num_rows > 0): ?>
            <table class="drugs-table">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Drug Name</th>
                        <th>Dosage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($drug = $drugs_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($drug['patient_name']) ?></td>
                            <td><?= htmlspecialchars($drug['drug_name']) ?></td>
                            <td><?= htmlspecialchars($drug['dosage']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data-message">
                <i class="fas fa-exclamation-circle"></i> No drugs found for the selected filters
            </div>
        <?php endif; ?>

        <a href="doctorDashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <script src="../assest/js/jquery.min.js"></script>
    <script src="../assest/js/popper.js"></script>
    <script src="../assest/js/bootstrap.min.js"></script>
    <script src="../assest/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messageBox = document.getElementById('message');
            const closeButton = document.getElementById('close-message');
            // Close message when clicking the close button
            closeButton.addEventListener('click', () => {
                messageBox.classList.add('hidden');
            });
            // Auto-hide message after the animation ends
            setTimeout(() => {
                messageBox.classList.add('hidden');
            }, 3000); // 3 seconds to match the animation duration
        });
    </script>
</body>
</html>