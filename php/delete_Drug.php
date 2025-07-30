<?php
session_start();
include '../DB/config.php';
// just login pharmacist can delete drug 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pharmacist') {
    header("Location: loginForm.php");
    exit();
}
if(isset($_GET['id'])){
    //store drug id 
    $drug_id = $_GET['id'];
    //delete it from drugs table 
    $stmt = $con->prepare("DELETE FROM Drugs WHERE id = ?");
    $stmt->bind_param("i", $drug_id);
    if (!$stmt->execute()) {
       die("Error deleting drug.");
    }
    header("Location: pharmacistDashboard.php?message=Drug+deleted+successfully.");
    exit();
}else{
    echo "No Drug id given";
}
?>
