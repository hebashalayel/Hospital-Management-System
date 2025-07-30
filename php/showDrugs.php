<?php
session_start();
include '../DB/config.php';
//only login doctor can assign and remove from into / from patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: loginForm.php");
    exit();
}

// Check if patient id is given
if (!isset($_GET['id'])) {
    echo "Error: Patient ID is not specified.";
    exit();
}
$patient_id = $_GET['id'];
$message = "";
//handle assign drug to patient
if (isset($_POST['assign_drug'])) {
    $drug_id = $_POST['drug_id'];
    //Check if the drug is already assigned
    $check_stmt = $con->prepare("SELECT * FROM patientdrug WHERE pat_id = ? AND drug_id = ?");
    $check_stmt->bind_param("ii", $patient_id, $drug_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows > 0) {
        $message = "This drug is already assigned to the patient.";
    } else {
        //insert into patientdrug table 
        $stmt = $con->prepare("INSERT INTO patientdrug (pat_id, drug_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $patient_id, $drug_id);
        if ($stmt->execute()) {
            $message = "Drug assigned to patient successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}

// handle remove drug from patient
if (isset($_POST['remove_drug'])) {
    $drug_id = $_POST['drug_id'];
    //delete specific drug from specific patient  
    $stmt = $con->prepare("DELETE FROM patientdrug WHERE pat_id = ? AND drug_id = ?");
    $stmt->bind_param("ii", $patient_id, $drug_id);
    if ($stmt->execute()) {
        $message = "Drug removed successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

// select all drugs to show in list that doctor can choose the drug to assign to patient
$drugs_stmt = $con->prepare("SELECT * FROM Drugs");
$drugs_stmt->execute();
$all_drugs = $drugs_stmt->get_result();

//select assigned drugs  to specific patient to show in table and can remove drug from it 
//make join to get the drug name and dosage to show in table (name , dosage not in patientdrug table)
$assigned_drugs_stmt = $con->prepare("SELECT d.id, d.name, d.dosage FROM Drugs d JOIN patientdrug pd ON d.id = pd.drug_id WHERE pd.pat_id = ?");
$assigned_drugs_stmt->bind_param("i", $patient_id);
$assigned_drugs_stmt->execute();
$assigned_drugs = $assigned_drugs_stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
    <title>Assign Drugs to Patient</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/maincss.css">
    <link rel="stylesheet" href="../css/showdrugs.css">
</head>
<body>
    <h2><i class="fa-solid fa-pills"></i> Assign Drugs to Patient.</h2>
    <hr> <!-- Separator line -->
    <form method="POST" style="margin-bottom: 20px; text-align: center;">
        <select name="drug_id" required>
            <option value="">Select Drug</option>
            <!-- show drug name in list as option-->
            <?php while ($drug = $all_drugs->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($drug['id']) ?>"><?= htmlspecialchars($drug['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="assign_drug" class="btn btn-info">Assign</button>
    </form>
<?php if ($message): ?>
    <div id="message" class="alert alert-info">
    <span id="message-text"><?= htmlspecialchars($message) ?></span>
    <button id="close-message" class="close-btn">&times;</button>
    <div class="loading-line"></div>
</div>
            <?php endif; ?>
    <table class="drugs-table">
        <thead>
            <tr>
                <th>Drug Name</th>
                <th>Dosage</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- each row in table contain of name and dosage for each assign drug -->
            <?php while ($assigned_drug = $assigned_drugs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($assigned_drug['name']) ?></td>
                    <td><?= htmlspecialchars($assigned_drug['dosage']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to remove this drug?');">
                            <input type="hidden" name="drug_id" value="<?= htmlspecialchars($assigned_drug['id']) ?>">
                            <button type="submit" name="remove_drug" class="btn btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="button-container">
        <a href="doctorDashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
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