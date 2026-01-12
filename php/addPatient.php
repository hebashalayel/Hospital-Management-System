<?php
session_start();
//only doctor can add patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: loginForm.php");
    exit();
}

require '../DB/config.php';
// Initialize variables to keep form inputs
$name = $age = $gender = $problem = $phoneNumber = $email = $entranceDate =$password= "";
$nameError = $ageError = $genderError = $problemError = $phoneNumberError = $entranceDateError =$passwordError= $emailerror ="";
$hashedPassword="";
$message = "";
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isValid = true;
    // check name
    if (empty($_POST['name'])) {
        $nameError = "Name is required.";
        $isValid = false;
    } else {
        $name = htmlspecialchars($_POST['name']);
    }

    // check email
    if (empty($_POST['email'])) {
        $emailerror = "Email is required.";
        $isValid = false;
    } else {
        $email = htmlspecialchars($_POST['email']);
    }
    if (empty($_POST['password'])) {
    $passwordError = "Password is required.";
    $isValid = false;
    } else {
        //save to password to show in form in with exactly input length not show the hashed password
    $password=$_POST['password'];
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // check age
    if (empty($_POST['age'])) {
        $ageError = "Age is required.";
        $isValid = false;
    } else {
        $age = htmlspecialchars($_POST['age']);
    }

    // check gender
    if (empty($_POST['gender'])) {
        $genderError = "Gender is required.";
        $isValid = false;
    } else {
        $gender = htmlspecialchars($_POST['gender']);
    }

    // check phone number
    if (empty($_POST['phoneNumber'])) {
        $phoneNumberError = "PhoneNumber is required.";
        $isValid = false;
    } else {
        $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    }

    // check entrance date
    if (empty($_POST['entranceDate'])) {
        $entranceDateError = "EntranceDate is required.";
        $isValid = false;
    } else {
        $entranceDate = htmlspecialchars($_POST['entranceDate']);
    }

    // check problem
    if (empty($_POST['problem'])) {
        $problemError = "Problem is required.";
        $isValid = false;
    } else {
        $problem = htmlspecialchars($_POST['problem']);
    }

        // if there are no errors 
        if ($isValid) {
        //insert the patient login data into user table to allow to his/her make login 
        $stmt = $con->prepare("INSERT INTO Users (email, password, role) VALUES (?, ?, 'patient')");
        if ($stmt === false) {
           die("Error preparing statement: " . $con->error);
        }
       $stmt->bind_param("ss", $email, $hashedPassword);
        //if inserted successfully to user then insert into patient tabel 
        if ($stmt->execute()) {
            $user_id = $con->insert_id; 
            $stmt = $con->prepare("INSERT INTO Patients (name, age, gender, problem, phoneNumber, id, email, entranceDate,password) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
           if ($stmt === false) {
               die("Error preparing statement: " . $con->error);
           }
            $stmt->bind_param("sisssssss", $name, $age, $gender, $problem, $phoneNumber, $user_id, $email, $entranceDate,$hashedPassword);
           
            if ($stmt->execute()) {
                //after insert into patient table must add to patientdoctor table 
                $patient_id = $con->insert_id;
                //get doctor id from session 
                $doctor_id = $_SESSION['doctor_id'];
                $stmt = $con->prepare("INSERT INTO patientdoctor (pat_id, doc_id) VALUES (?, ?)");
                if ($stmt === false) {
                    die("Error preparing statement: " . $con->error);
                }
                $stmt->bind_param("ii", $patient_id, $doctor_id);
                if ($stmt->execute()) {
                    $message = "Patient added successfully.";
                    $name = $age = $gender = $problem = $phoneNumber = $email = $emailerror = $entranceDate =$password= "";
                    //return to doctor dashboard 
                    echo "<script>setTimeout(function(){ window.location.href = 'doctorDashboard.php'; }, 2000);</script>";
                } else {
                    echo "<script>alert('Error inserting into patientdoctor table: " . $stmt->error . "');</script>";
               }
            } else {
                echo "<script>alert('Error inserting into Patients table: " . $stmt->error . "');</script>";
            }
            // if not inserted to user table 
        } else {
            if ($stmt->errno == 1062) { 
                $emailerror = "This email is already used by another user.";
            } else {
                echo "<script>alert('Error inserting into Users table: " . $stmt->error . "');</script>";
            }
        }
       $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Patient</title>
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/add-patient.css">
</head>
<body oncontextmenu="return false" class="snippet-body">
    <div class="wrapper">
        <div class="form">
            <h1 class="title">Add Patient</h1>
                 <?php if ($message): ?>
               <div id="message" class="alert alert-info">
    <span id="message-text"><?= htmlspecialchars($message) ?></span>
    <button id="close-message" class="close-btn">&times;</button>
    <div class="loading-line"></div>
</div>
            <?php endif; ?>
            <form action="" class="myform" method="post">
                <div class="control-from">
                    <label for="name">Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>"  />
                    <span class="text-danger"><?= $nameError; ?></span>
                </div>
                <div class="control-from ">
                    <label for="email">Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"  />
                    <?php if (!empty($emailerror)): ?>
                        <span class="text-danger"><?php echo $emailerror; ?></span>
                    <?php endif; ?>
                </div>

                <div class="control-from">
                    <label for="password">Password *</label>
                    <input type="password" name="password"  value="<?= htmlspecialchars($password) ?>" />
                    <span class="text-danger"><?= $passwordError; ?></span>
                </div>

                <div class="control-from">
                    <label for="age">Age *</label>
                    <input type="text" name="age" value="<?= htmlspecialchars($age) ?>"  />
                    <span class="text-danger"><?= $ageError; ?></span>
                </div>

                <div class="select-wrapper">
                    <label for="gender">Gender *</label>
                    <select class="form-select" name="gender" >
                        <option value="" disabled selected>Choose Gender</option>
                        <option value="male" <?= $gender == 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $gender == 'female' ? 'selected' : '' ?>>Female</option>
                    </select>
                    <span class="text-danger"><?= $genderError; ?></span>
                    <i class="fa-solid fa-caret-down"></i>
                </div>

                <div class="control-from">
                    <label for="phoneNumber">Phone Number *</label>
                    <input type="text" name="phoneNumber" value="<?= htmlspecialchars($phoneNumber) ?>"  />
                    <span class="text-danger"><?= $phoneNumberError; ?></span>
                </div>

                <div class="full-width">
                    <label for="entranceDate">Entrance Date *</label>
                    <input type="date" name="entranceDate" value="<?= htmlspecialchars($entranceDate) ?>"  />
                    <span class="text-danger"><?= $entranceDateError; ?></span>
                </div>

                <div class="full-width">
                    <label for="problem" class="form-label">Problem *</label>
                    <textarea class="form-control" name="problem" rows="3" ><?= htmlspecialchars($problem) ?></textarea>
                    <span class="text-danger"><?= $problemError; ?></span>
                </div>

                <div class="button">
                    <button type="submit" id="register">Add</button>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript"></script>
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
