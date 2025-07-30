<?php
session_start();
require '../DB/config.php';
//initialize for store values and error messages
$name = $dosage = $productionDate = $expiryDate = "";
$nameError = $dosageError = $productionDateError = $expiryDateError = "";
$message = "";
// just login pharmacist can add drug 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pharmacist') {
    header("Location: loginForm.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;
    //check name
    if (empty($_POST["name"])) {
        $nameError = "This field is required.";
        $isValid = false;
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }
    //check dosage
    if (empty($_POST["dosage"])) {
        $dosageError = "This field is required.";
        $isValid = false;
    } else {
        $dosage = htmlspecialchars($_POST["dosage"]);
    }
    //check productiondate
    if (empty($_POST["productionDate"])) {
        $productionDateError = "This field is required.";
        $isValid = false;
    } else {
        $productionDate = htmlspecialchars($_POST["productionDate"]);
    }
    //check expirydate
    if (empty($_POST["expiryDate"])) {
        $expiryDateError = "This field is required.";
        $isValid = false;
    } else {
        $expiryDate = htmlspecialchars($_POST["expiryDate"]);
    }
    //check the production date before expiryDate
    if ($isValid && strtotime($productionDate) >= strtotime($expiryDate)) {
        $expiryDateError = "Be attention! Expiry Date must be after Production Date.";
        $isValid = false;
    }
    //if there are no errors insert into drugs table 
    if ($isValid) {
        $stmt = $con->prepare("INSERT INTO Drugs (name, dosage, productionDate, expiryDate) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $dosage, $productionDate, $expiryDate);
        if ($stmt->execute()) {
            $message = "Drug added successfully.";
            $name = $dosage = $productionDate = $expiryDate = "";
            echo "<script>setTimeout(function(){ window.location.href = 'pharmacistDashboard.php'; }, 2000);</script>";
        } else {
            $message = "Error adding drug: " . $stmt->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Add Drug</title>
    <link href="../assest/img/logo.avif" rel="icon">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/add-drug.css">
     
</head>
<body oncontextmenu="return false" class="snippet-body">
<div class="wrapper">
        <div class="form">
            <h1 class="title">Add Drug</h1>
    <?php if ($message): ?>
               <div id="message" class="alert alert-info">
    <span id="message-text"><?= htmlspecialchars($message) ?></span>
    <button id="close-message" class="close-btn">&times;</button>
    <div class="loading-line"></div>
</div>
            <?php endif; ?>
    <form method="POST" class="myform" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="showLoadingSpinner()">
        <div class="full-width">
            <label for="name">Drug Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" >
            <span class="text-black-50"><?= $nameError; ?></span>
        </div>
        <div class="full-width">
            <label for="dosage">Dosage:</label>
            <input type="text" class="form-control" id="dosage" name="dosage" value="<?= htmlspecialchars($dosage) ?>" >
            <span class="text-black-50"><?= $dosageError; ?></span>
        </div>
        <div class="full-width">
            <label for="productionDate">Production Date:</label>
            <input type="date" class="form-control" id="productionDate" name="productionDate" value="<?= htmlspecialchars($productionDate) ?>" >
            <span class="text-black-50"><?= $productionDateError; ?></span>
        </div>
        <div class="full-width">
            <label for="expiryDate">Expiry Date:</label>
            <input type="date" class="form-control" id="expiryDate" name="expiryDate" value="<?= htmlspecialchars($expiryDate) ?>" >
            <span class="text-black-50"><?= $expiryDateError; ?></span>
        </div>
        <div class="button">
                    <button id="register" type="submit">Add</button>
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
