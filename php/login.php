<?php
session_start();
require '../DB/config.php';
 //must fill login form before 
if (!isset($_SESSION['userData'])) {
    header("Location:loginForm.php");
    exit();
}

//get user data from session
$user_data = $_SESSION['userData'];
$email = $user_data['email']; 
$password = $user_data['password'];
unset($_SESSION['userData']);

 // get user with login email 
$stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    //check if password of this email correct or not 
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // check the role to determine which dashboard open 
        if ($user['role'] == 'doctor') {
            //select the id store it in session to show the relaterd data to this user 
            $stmt = $con->prepare("SELECT id FROM Doctors WHERE email = ?");
            $stmt->bind_param("s", $user['email']);
            $stmt->execute();
            $doctor_result = $stmt->get_result();
            if ($doctor_result->num_rows > 0) {
                $doctor = $doctor_result->fetch_assoc();
                $_SESSION['doctor_id'] = $doctor['id'];
            }
            header("Location: doctorDashboard.php");
        } elseif ($user['role'] == 'patient') {
            $stmt = $con->prepare("SELECT id FROM patients WHERE email = ?");
            $stmt->bind_param("s", $user['email']);
            $stmt->execute();
            $patient_result = $stmt->get_result();
            if ($patient_result->num_rows > 0) {
                $patient = $patient_result->fetch_assoc();
                $_SESSION['patient_id'] = $patient['id'];
            }
            header("Location: patientDashboard.php");
        } elseif ($user['role'] == 'pharmacist') {
            $stmt = $con->prepare("SELECT id FROM pharmacists WHERE email = ?");
            $stmt->bind_param("s", $user['email']);
            $stmt->execute();
            $pharmacist_result = $stmt->get_result();
            if ($pharmacist_result->num_rows > 0) {
                $pharmacist = $pharmacist_result->fetch_assoc();
                $_SESSION['pharmacist_id'] = $pharmacist['id'];
            }
            header("Location: pharmacistDashboard.php");
        }
        exit();
        // if password not correct but not specify the message for just error password for more secutity 
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            $_SESSION['userData'] = $user_data;
            header("Location: loginForm.php");
            exit();
    }
    //if the query not return user that mean the email not found  
} else {
    //return the error in session and the data to show it in form not clear it from input fields 
    $_SESSION['error'] = "The email address is not registered. Please register first.";
    $_SESSION['userData'] = $user_data;
    header("Location: loginForm.php");
    exit();
}
?>