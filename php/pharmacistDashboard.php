<?php
session_start();
include '../DB/config.php';
// must be login as pharmacist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pharmacist') {
    header("Location: loginForm.php");
    exit();
}
// get the pharmacist id from the session
$pharmacist_id = $_SESSION['pharmacist_id'];
// select drugs to show in table  
$drugs_stmt = $con->prepare("SELECT * FROM Drugs");
$drugs_stmt->execute();
$all_drugs = $drugs_stmt->get_result();
//store notification message to show it 
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : null;
?>
<!doctype html>
<html lang="en">
  <head>
  	<title>Pharmacist Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="../css/maincss.css">
        <link rel="stylesheet" href="../css/pharmacistDashboard.css">
   
  </head>
  <body>
        <!-- Page Content  -->
<div class="table-container">
    <div class="table-header">
        <div class="title-container">
            <h2><i class="fa-solid fa-syringe"></i> Drugs Data</h2>
        </div>
        <?php if ($message): ?>
            <div id="message" class="alert alert-info">
                <span id="message-text"><?= htmlspecialchars($message) ?></span>
                <button id="close-message" class="close-btn">&times;</button>
                <div class="loading-line"></div>
            </div>
        <?php endif; ?>
        <div class="button-container">
            <button id="addDrugButton" class="add-drug-button" onclick="window.location.href='addDrug.php';">
                <i class="fa fa-plus"></i> Add Drug
            </button>
            <button id="logoutButton" class="logout-button" onclick="window.location.href='logout.php';">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </div>
    <hr>
    <?php if ($all_drugs->num_rows > 0): ?>
        <table class="drug-table">
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($drug = $all_drugs->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($drug['name']) ?>
                    </td>
                    <td class="action-icons">
                        <a href="update_Drug.php?id=<?= $drug['id'] ?>" title="Update"><i class="fa fa-edit update-icon"></i></a>
                        <a href="delete_Drug.php?id=<?= $drug['id'] ?>" title="Delete" onclick="return confirm('Are you sure?')">
                            <i class="fa fa-trash delete-icon"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data-message">
            <h3>No Drugs added until now</h3>
        </div>
    <?php endif; ?>
</div>

        </div>  
    <script src="../js/jquery.min.js"></script>
    <script src="../js/popper.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
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
    </script>
  </body>
</html>
