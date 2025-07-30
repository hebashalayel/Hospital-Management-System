<?php
//initialize variables to strore input values and error message for each input (validate=> serverside)
$first_name = $last_name = $email = $phone = $password = $role = "";
$first_nameError = $last_nameError = $emailError = $phoneError = $passwordError = $roleError = "";

// Check inputs form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid=true;
    //check firstname
    if (empty($_POST['first_name'])) {
        $first_nameError = "First name is required.";
        $isValid = false;
    }else{
        $first_name = htmlspecialchars($_POST["first_name"]);
    }
    //check lastname
    if (empty($_POST['last_name'])) {
        $last_nameError = "Last name is required.";
        $isValid = false;
    }else{
        $last_name = htmlspecialchars($_POST["last_name"]);
    }
    //check email 
    if (empty($_POST['email'])) {
        $emailError = "Email is required.";
        $isValid = false;
    }else{
      $email = htmlspecialchars($_POST["email"]);
    }
    //check phoneNumber
    if (empty($_POST['phone'])) {
        $phoneError = "Phone number is required.";
        $isValid = false;
    }else{
      $phone = htmlspecialchars($_POST["phone"]);
    }
    //check password
    if (empty($_POST['password'])) {
        $passwordError = "Password is required.";
        $isValid = false;
    }else{
      $password = htmlspecialchars($_POST["password"]);
    }
    //check role
    if (empty($_POST['role'])) {
        $roleError = "Role selection is required.";
        $isValid = false;
    }else{
      $role = htmlspecialchars($_POST["role"]);
    }

    // If there are no errors, store data of user in session and send to register.php to store in db
    if ($isValid) {
        session_start();
        //store asscosiative array of inputs in session if all inputs isvalid 
        $_SESSION['userData'] = $_POST;
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registration Page </title>
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
  <section class="vh-100">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black" style="border-radius: 25px;">
            <div class="card-body p-md-5">
              <div class="row justify-content-center">
                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
  
                  <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Register</p>
  
                  <form class="mx-1 mx-md-4" method="post" action="">
  
                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                      <div data-mdb-input-init class="form-outline flex-fill mb-0">
                        <input type="text" name="first_name" class="form-control" placeholder="First Name"  value="<?= htmlspecialchars($first_name) ?>"/>
                        <span class="text-danger"><?= $first_nameError ?></span>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                      <div data-mdb-input-init class="form-outline flex-fill mb-0">
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="<?= htmlspecialchars($last_name) ?>" />
                        <span class="text-danger"><?= $last_nameError ?></span>
                      </div>
                    </div>
  
                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                      <div data-mdb-input-init class="form-outline flex-fill mb-0">
                        <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?= htmlspecialchars($email) ?>"/>
                        <span class="text-danger"><?= $emailError ?></span>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-phone fa-lg me-3 fa-fw" ></i> 
                      <div data-mdb-input-init class="form-outline flex-fill mb-0">
                        <input type="phone " name="phone" class="form-control" placeholder="Your Phone" value="<?= htmlspecialchars($phone) ?>"/>
                        <span class="text-danger"><?= $phoneError ?></span>
                      </div>
                    </div>
  
                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                      <div data-mdb-input-init class="form-outline flex-fill mb-0">
                        <input type="password" name="password" class="form-control" placeholder="Password" value="<?= htmlspecialchars($password) ?>" />
                        <span  class="text-danger"><?= $passwordError ?></span>
                      </div>
                    </div>
                    
                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-stethoscope fa-lg me-3 fa-fw"></i>
                      <div data-mdb-input-init class="form-outline flex-fill mb-0">
                        <select class="form-select" name="role">
    <option value="" disabled selected>Choose Career</option>
    <option value="doctor" <?= isset($role) && $role == 'doctor' ? 'selected' : '' ?>>Doctor</option>
    <option value="pharmacist" <?= isset($role) && $role == 'pharmacist' ? 'selected' : '' ?>>Pharmacist</option>
</select>
                        <span class="text-danger"><?= $roleError ?></span>

                      </div>
                    </div>
                    
                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                        <input class="btn btn-dark btn-lg" type="submit" value="Register" />
                    </div>
  
                  </form>
  
                </div>
                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                  <img width="550" height="600" src="https://static.vecteezy.com/system/resources/previews/034/030/952/non_2x/patient-having-consultation-with-woman-doctor-in-hospital-medical-consultation-in-clinic-cartoon-character-illustration-vector.jpg" 
                    class="img-fluid" alt="Sample image">
                </div>
              </div>
            </div>
          </div>  
        </div>
      </div>
    </div>
  </section>
</body>
</html>