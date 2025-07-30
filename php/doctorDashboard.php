<?php
session_start();
include '../DB/config.php';

// must be doctor and login before open this page 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: loginForm.php");
    exit();
}

// get doctor id from session 
$doctor_id = $_SESSION['doctor_id'];

// select patients assigned to the login doctor
$stmt = $con->prepare("SELECT * FROM Patients WHERE id IN (SELECT pat_id FROM patientdoctor WHERE doc_id = ?)");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$patients = $stmt->get_result();

//store notification message to show it 
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : null;
?>
<!doctype html>
<html lang="en">
<head>
    <title>Doctor Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../css/maincss.css">
    <link rel="stylesheet" href="../css/doctorDashboard.css">
</head>
<body>
    <div class="table-container">
        <div class="table-header">
            <div class="title-container">
                <h2><i class="fa fa-users"></i> Patients Data</h2>
            </div>
             <?php if ($message): ?>
               <div id="message" class="alert alert-info">
    <span id="message-text"><?= htmlspecialchars($message) ?></span>
    <button id="close-message" class="close-btn">&times;</button>
    <div class="loading-line"></div>
</div>
            <?php endif; ?>
            <!-- <div class="button-container">
                <br><hr>
                <button id="addPatientButton" class="add-patient-button" onclick="window.location.href='addPatient.php';">
                    <i class="fa fa-plus"></i> Add Patient
                </button>
            </div> -->
             <div class="button-container">
                <button class="add-patient-button" onclick="window.location.href='addPatient.php';">
                    <i class="fa fa-plus"></i> Add Patient
                </button>
                <button class="show-drugs-button" onclick="window.location.href='patients_drugs.php';">
                   <i class="fa fa-pills"></i> Show Patients Drugs
                </button>
                <button class="logout-button" onclick="logout();">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </button>
            </div>
            <?php if ($patients->num_rows > 0): ?>
                <table class="patient-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Problem</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = $patients->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($patient['name']) ?></td>
                                <td><?= htmlspecialchars($patient['age']) ?></td>
                                <td><?= htmlspecialchars($patient['gender']) ?></td>
                                <td><?= htmlspecialchars($patient['problem']) ?></td>
                                <td><?= htmlspecialchars($patient['phoneNumber']) ?></td>
                                <td class="action-icons">
                                    <a href="update_patient.php?id=<?= $patient['id'] ?>" title="Edit"><i class="fa fa-edit edit-icon"></i></a>
                                    <a href="delete_patient.php?id=<?= $patient['id'] ?>" title="Delete" onclick="return confirm('Are you sure?')"><i class="fa fa-trash delete-icon"></i></a>
                                    <a href="showDrugs.php?id=<?= $patient['id'] ?>" title="Drugs"><i class="fa-solid fa-pills drugs-icon"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data-message">
                    <h4>No patients added until now</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="../assest/js/jquery.min.js"></script>
    <script src="../assest/js/popper.js"></script>
    <script src="../assest/js/bootstrap.min.js"></script>
    <script src="../assest/js/main.js"></script>
    <script>
        // JavaScript to handle the close of the success message
        document.addEventListener('DOMContentLoaded', () => {
            const messageBox = document.getElementById('message');
            const closeButton = document.getElementById('close-message');
            // Close message when clicking the close button
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    messageBox.classList.add('hidden');
                });
            }
            // Auto-hide message after 3 seconds
            if (messageBox) {
                setTimeout(() => {
                    messageBox.classList.add('hidden');
                }, 3000);
            }
        });
        function logout() {
            window.location.href = 'logout.php'; 
        }
    </script>
</body>
</html>

