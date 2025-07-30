<?php
session_start();
require '../DB/config.php';
//check patient id 
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    // select the email of patient to remove it from users table 
    $stmt = $con->prepare("SELECT email FROM Patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();

    if ($patient) {
        $email = $patient['email'];

        // delete from patientdoctor table
        $stmt = $con->prepare("DELETE FROM patientdoctor WHERE pat_id = ?");
        $stmt->bind_param("i", $patient_id);
        if (!$stmt->execute()) {
            echo "Error deleting patientdoctor.";
            exit();
        }

        // delete from patients table
        $stmt = $con->prepare("DELETE FROM Patients WHERE id = ?");
        $stmt->bind_param("i", $patient_id);
        if (!$stmt->execute()) {
            echo "Error deleting patient.";
            exit();
        }

        // delete from users table
        $stmt = $con->prepare("DELETE FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            echo "Error deleting user.";
            exit();
        }
        header("Location: doctorDashboard.php?message=Patient+deleted+successfully.");
        exit();
    } else {
        echo "patient not exist";
    }
} else {
    echo "No patient id given";
}
?>

