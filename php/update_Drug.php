<?php
session_start();
require '../DB/config.php';

// Only login pharmacist can update drug
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pharmacist') {
    header("Location: loginForm.php");
    exit();
}

$message = '';
$nameError = $dosageError = $productionDateError = $expiryDateError = "";
$name = $dosage = $productionDate = $expiryDate = "";

// Check if the drug ID is provided
if (isset($_GET['id'])) {
    $drug_id = $_GET['id'];
    
    // Get drug data to populate the form
    $stmt = $con->prepare("SELECT * FROM Drugs WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }
    $stmt->bind_param("i", $drug_id);
    $stmt->execute();
    $drug = $stmt->get_result()->fetch_assoc();

    // fill form input fields 
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($drug['name']);
    $dosage = isset($_POST['dosage']) ? htmlspecialchars($_POST['dosage']) : htmlspecialchars($drug['dosage']);
    $productionDate = isset($_POST['productionDate']) ? htmlspecialchars($_POST['productionDate']) : htmlspecialchars($drug['productionDate']);
    $expiryDate = isset($_POST['expiryDate']) ? htmlspecialchars($_POST['expiryDate']) : htmlspecialchars($drug['expiryDate']);
} else {
    header("Location: pharmacistDashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isValid = true;

    // check Name
    if (empty($_POST["name"])) {
        $nameError = "This field is required.";
        $isValid = false;
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    // check Dosage
    if (empty($_POST["dosage"])) {
        $dosageError = "This field is required.";
        $isValid = false;
    } else {
        $dosage = htmlspecialchars($_POST["dosage"]);
    }

    // check Production Date
    if (empty($_POST["productionDate"])) {
        $productionDateError = "This field is required.";
        $isValid = false;
    } else {
        $productionDate = htmlspecialchars($_POST["productionDate"]);
    }

    // check Expiry Date
    if (empty($_POST["expiryDate"])) {
        $expiryDateError = "This field is required.";
        $isValid = false;
    } else {
        $expiryDate = htmlspecialchars($_POST["expiryDate"]);
    }

    // Check if production date is before expiry date
    if ($isValid && strtotime($productionDate) >= strtotime($expiryDate)) {
        $expiryDateError = "Expiry Date must be after Production Date.";
        $isValid = false;
    }
    
    if ($isValid) {
        // Update drug in the database
        $stmt = $con->prepare("UPDATE Drugs SET name = ?, dosage = ?, productionDate = ?, expiryDate = ? WHERE id = ?");
            if ($stmt === false) {
                die("Error preparing statement: " . $con->error);
            }
            $stmt->bind_param("ssssi", $name, $dosage, $productionDate, $expiryDate, $drug_id);
            if ($stmt->execute()) {
                $message = "Drug updated successfully.";
                echo "<script>
                        // Display the success message and redirect after 2 seconds
                        setTimeout(function() {document.getElementById('message').classList.add('hidden');
                        window.location.href = 'pharmacistDashboard.php';}, 2000);
                    </script>";
            } else {
                echo "Error updating drug: " . $stmt->error;
            }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Update Drug</title>
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/add-drug.css">
</head>
<body>
    <div class="wrapper">
        <div class="form">
            <h1 class="title">Update Drug</h1>
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
                    <input type="text" name="name" value="<?php echo $name; ?>"  />
                    <span><?php echo $nameError; ?></span>
                </div>

                <div class="control-from">
                    <label for="dosage">Dosage *</label>
                    <input type="text" name="dosage" value="<?php echo $dosage; ?>"  />
                    <span><?php echo $dosageError; ?></span>
                </div>

                <div class="control-from">
                    <label for="productionDate">ProductionDate *</label>
                    <input type="date" name="productionDate" value="<?php echo $productionDate; ?>"  />
                    <span><?php echo $productionDateError; ?></span>
                </div>

                <div class="control-from">
                    <label for="expiryDate">ExpriyDate *</label>
                    <input type="date" name="expiryDate" value="<?php echo $expiryDate; ?>"  />
                    <span><?php echo $expiryDateError; ?></span>
                </div>

                <div class="button">
                    <button type="submit">Update</button>
                </div>
            </form>
        </div>
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