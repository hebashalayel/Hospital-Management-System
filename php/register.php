<?php
session_start();
//must fill form before reach this page 
if (!isset($_SESSION['userData'])){
    header("Location: registerForm.php");
    exit();
}
//store userdate from session in associative array 
$user_data = $_SESSION['userData'];
unset($_SESSION['userData']);

require '../DB/config.php';

//get user data from session
$first_name = $user_data['first_name'];
$last_name = $user_data['last_name'];
//concatenate to store it as fullname in database there is one col. for name 
$name = $first_name . ' ' . $last_name;
$email = $user_data['email'];
$phone =$user_data['phone'];
//hash password to more security 
$password = password_hash($user_data['password'], PASSWORD_DEFAULT);
$role = $user_data['role'];
//insert user register into users table create it to search only in one table when make login after register 
$sql = "INSERT INTO Users (email, password, role) VALUES (?, ?, ?)";
if (!$stmt = $con->prepare($sql)) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("sss", $email, $password, $role);
// if inserted successfully into users then insert to specific table acordint to role
if ($stmt->execute()) {
    $user_id = $con->insert_id;
    //check the role to know where to insert user into doctor table or pharmacist table 
    if ($role === "doctor") {
        $sql = "INSERT INTO Doctors (name, email, phone_number,password) VALUES (?, ?, ?,?)";
    } elseif ($role === "pharmacist") {
        $sql = "INSERT INTO Pharmacists (name, email, phone_number,password) VALUES (?, ?, ?,?)";
    }
    if (!$stmt = $con->prepare($sql)) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("ssss", $name, $email, $phone,$password);
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='../html/index.html';</script>";
    } else {
        echo "Error inserting into ".$role." table: " . $stmt->error;
    }
} else {
    //if failed to insert into user table check if it is beacause email to show error message 
    if ($stmt->errno == 1062) {
       echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='registerForm.php';</script>";
    } else {
        echo "<script>alert('Error inserting into Users table: " . $stmt->error . "'); window.location.href='registerForm.php';</script>";
    }
}
$con->close();
?>