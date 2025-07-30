<?php
session_start();
require '../DB/config.php';

// Initialize message and error variables
$message = "";
$nameError = $emailError = $ageError = $genderError = $problemError = $phoneNumberError =$passwordError ="";

// Make sure just doctor or patient make login both can update patient data
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'doctor' && $_SESSION['role'] != 'patient')) {
    header("Location: loginForm.php");
    exit();
}

// Check if patient id is given
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    //select patient data to show his/her data in form to update 
    $stmt = $con->prepare("SELECT * FROM Patients WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $patient = $stmt->get_result()->fetch_assoc();
    if (!$patient) {
        header("Location: doctorDashboard.php");
        exit();
    }
} else {
    header("Location: doctorDashboard.php");
    exit();
}
// initialize input variables with patient data 
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($patient['name']);
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($patient['email']);
$age = isset($_POST['age']) ? htmlspecialchars($_POST['age']) : htmlspecialchars($patient['age']);
$gender = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : htmlspecialchars($patient['gender']);
$problem = isset($_POST['problem']) ? htmlspecialchars($_POST['problem']) : htmlspecialchars($patient['problem']);
$phoneNumber = isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : htmlspecialchars($patient['phoneNumber']);
$password=isset($_POST['password']) ? htmlspecialchars($_POST['password']) : htmlspecialchars($patient['password']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isValid = true;

    // check Name
    if (empty($_POST['name'])) {
        $nameError = "Name is required.";
        $isValid = false;
    } else {
        $name = htmlspecialchars($_POST['name']);
    }

    // check Email
    if (empty($_POST['email'])) {
        $emailError = "Email is required.";
        $isValid = false;
    } else {
        $email = htmlspecialchars($_POST['email']);
    }

    // check Age
    if (empty($_POST['age'])) {
        $ageError = "Age is required.";
        $isValid = false;
    } elseif (!is_numeric($_POST['age']) || $_POST['age'] <= 0) {
        $ageError = "Age must be a positive number.";
        $isValid = false;
    } else {
        $age = htmlspecialchars($_POST['age']);
    }

    // check Gender
    if (empty($_POST['gender'])) {
        $genderError = "Gender is required.";
        $isValid = false;
    } else {
        $gender = htmlspecialchars($_POST['gender']);
    }

    // check Phone Number
    if (empty($_POST['phoneNumber'])) {
        $phoneNumberError = "Phone Number is required.";
        $isValid = false;
    } elseif (!preg_match('/^[0-9]+$/', $_POST['phoneNumber'])) {
        $phoneNumberError = "Invalid phone number format (numbers only).";
        $isValid = false;
    }
    else {
        $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    }

    // check Problem
    if (empty($_POST['problem'])) {
        $problemError = "Problem is required.";
        $isValid = false;
    } else {
        $problem = htmlspecialchars($_POST['problem']);
    }
    //check the password
    if (empty($_POST['password'])) {
    $passwordError = "Password is required.";
    $isValid = false;
} else {
    $password = htmlspecialchars($_POST['password']);
    $password = password_hash($password, PASSWORD_DEFAULT); 
}
            if ($isValid) {
            // Check if the email already exists in the Users table
            $stmt = $con->prepare("SELECT id FROM Users WHERE email = ? AND id != (SELECT id FROM Patients WHERE id = ?)");
            if ($stmt === false) {
             die("Error preparing statement: " . $con->error);
            }
            $stmt->bind_param("si", $email, $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $emailError = "This email is already used by another user.";
            } else {
            // Update patient in the database
            $stmt = $con->prepare("UPDATE Patients SET name = ?, age = ?, gender = ?, problem = ?, phoneNumber = ? ,email=? ,password=? WHERE id = ?");
            if ($stmt === false) {
                die("Error preparing statement: " . $con->error);

            }
            $stmt->bind_param("sisssssi", $name, $age, $gender, $problem, $phoneNumber ,$email,$password, $patient_id);
            if ($stmt->execute()) {
                $message = "Patient updated successfully.";
                $role = $_SESSION['role'];
                echo "<script>setTimeout(function() {
                    if ('" . $role . "' == 'doctor') {
                    window.location.href = 'doctorDashboard.php';
                    } else if ('" . $role . "' == 'patient') {
                        window.location.href = 'patientDashboard.php';}}, 2000);
                    </script>";
            } else {
                echo "<script>alert('Error updating patient: " . $stmt->error . "');</script>";
            }
            }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Update Patient</title>
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/add-patient.css">
</head>
<body>
    <div class="wrapper">
        <div class="form">
            <h1 class="title">Update Patient</h1>

            <!-- Success Message -->
            <?php if ($message): ?>
            <div id="message" class="alert alert-info">
                <span id="message-text"><?= htmlspecialchars($message) ?></span>
                <button id="close-message" class="close-btn">&times;</button>
                <div class="loading-line"></div>
            </div>
            <?php endif; ?>

            <form action="" method="POST" class="myform">
                <div class="control-from">
                    <label for="name">Name *</label>
                    <input type="text" name="name" value="<?php echo $name; ?>" />
                    <span class="text-danger"><?php echo $nameError; ?></span>
                </div>

                <div class="control-from">
                    <label for="email">Email *</label>
                    <input type="email" name="email" value="<?php echo $email ?>" />
                    <span class="text-danger"><?php echo $emailError; ?></span>
                </div>

                <div class="control-from">
                    <label for="password">Password *</label>
                    <input type="password" name="password" value="<?php echo $password ?>" />
                    <span class="text-danger"><?php echo $passwordError; ?></span>
                </div>

                <div class="control-from">
                    <label for="age">Age *</label>
                    <input type="text" name="age" value="<?php echo $age ?>" />
                    <span class="text-danger"><?php echo $ageError; ?></span>
                </div>

                <div class="select-wrapper">
                    <label for="gender">Gender *</label>
                    <select class="form-select" name="gender">
                        <option value="" disabled>Select Gender</option>
                        <option value="male" <?= ($gender == 'male') ? 'selected' : '' ?>>Male</option> <--- Use the variable
                        <option value="female" <?= ($gender == 'female') ? 'selected' : '' ?>>Female</option> <--- Use the variable
                    </select>
                    <span class="text-danger"><?= $genderError ?></span>
                    <i class="fa-solid fa-caret-down"></i>
                </div>

                <div class="control-from">
                    <label for="phoneNumber">Phone Number *</label>
                    <input type="text" name="phoneNumber" value="<?php echo $phoneNumber ?>" />
                    <span class="text-danger"><?php echo $phoneNumberError; ?></span>
                </div>

                <div class="full-width">
                    <label for="problem" class="form-label">Problem *</label>
                    <textarea class="form-control" name="problem" rows="3"><?php echo $problem ?></textarea>
                    <span class="text-danger"><?php echo $problemError; ?></span>
                </div>

                <div class="button">
                    <button type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => const messageBox = document.getElementById('message');
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