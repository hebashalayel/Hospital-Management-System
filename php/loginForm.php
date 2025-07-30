<?php
session_start();
// Check for errors from login.php
$loginError = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
//initialize variables to strore input values and error message for each input (validate => serverside)
$email = $password='';
$emailError = $passwordError ='';
// Retain the input values if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid=true;
    //check email input
    if (empty($_POST['email'])) {
        $isValid=false;
        $emailError = "Email is required.";
    }else{
        $email= htmlspecialchars($_POST['email']);
    }
    //check password input
    if (empty($_POST['password'])) {
        $isValid=false;
        $passwordError = "Password is required.";
    }else{
        $password=htmlspecialchars($_POST['password']);
    }
    // If there are no errors send data using session to login.php
    if ($isValid) {
        $_SESSION['userData'] = $_POST;
        header("Location:login.php");
        exit();
    }
} else {
    if(isset($_SESSION['userData'])) {
        $email = $_SESSION['userData']['email'] ?? '';
        $password = $_SESSION['userData']['password'] ?? '';
        unset($_SESSION['userData']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="../assest/img/logo.avif" rel="icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<section class="vh-100" style="background-color: #ffffff;">
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-11">
                <div class="card text-black" style="border-radius: 25px;">
                    <div class="card-body p-md-5">
                        <div class="row justify-content-center">

                            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1 order-2 order-lg-2 mt-5">
                                <form method="Post" action="">
                                    <!-- Email input -->
                                    <div data-mdb-input-init class="form-outline mb-4">
    <label class="form-label" for="email">Email address</label>
    <input type="email" id="email" name="email" class="form-control form-control-lg"
        placeholder="Enter a valid email address" value="<?= htmlspecialchars($email) ?>"/>
    <span class="text-danger"><?= $emailError ?></span>
</div>
                                    <!-- Password input -->
                                    <div data-mdb-input-init class="form-outline mb-3">
    <label class="form-label" for="password">Password</label>
    <input type="password" id="password" name="password" class="form-control form-control-lg"
        placeholder="Enter password" value="<?= htmlspecialchars($password) ?>"/>
    <span class="text-danger"><?= $passwordError ?></span>
    <span class="text-danger"><?= $loginError ?></span>
</div>
                                    <div class="text-center text-lg-start mt-4 pt-2">
                                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg"
                                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                                        <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="registerForm.php"
                                                class="link-danger">Register</a></p>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-10 col-lg-6 col-xl-5 order-1 order-lg-1">
                                <img src="https://qupapp.com/assets/img/services/00-07.png"
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