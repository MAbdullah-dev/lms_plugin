<?php 
require_once "./components/header.php";
require_once '../controllers/AuthController.php';

$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if ($_POST['formType'] === 'signup') {
        $controller->register(); 
    } elseif ($_POST['formType'] === 'login') {
        $controller->login(); 
    }
}
$errors = $controller->errors; // Use the class property to get errors
?>
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <!-- Nav tabs -->
        <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="signup-tab" data-bs-toggle="pill" data-bs-target="#signup" type="button" role="tab" aria-controls="signup" aria-selected="false">Signup</button>
            </li>
        </ul> 

        <!-- Tab content -->
        <div class="tab-content">
            <!-- Login Form -->
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                <form class="p-4 border rounded bg-light shadow-sm" method="POST" action="">
                    <h2 class="text-center text-primary">Login</h2>
                    <ul class="alert alert-danger">
                        <?php if(!empty($errors)): ?>
                        <?php foreach ($errors as $error) {  ?>
                        <li> <?= $error ?></li>
                        <?php } ?>
                        <?php endif?>
                    </ul>
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control border-primary" id="loginEmail" name="loginEmail" placeholder="Enter your email" >
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" class="form-control border-primary" id="loginPassword" name="loginPassword" placeholder="Enter your password" >
                    </div>
                    <!-- Hidden field to identify login form -->
                    <input type="hidden" name="formType" value="login">
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>

            <!-- Signup Form -->
            <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
                <form class="p-4 border rounded bg-light shadow-sm" method="POST" action="">
                    <h2 class="text-center text-primary">Signup</h2>
                    <div class="mb-3">
                        <label for="signupName" class="form-label">Full Name</label>
                        <input type="text" class="form-control border-primary" id="signupName" name="signupName" placeholder="Enter your full name" >
                    </div>
                    <div class="mb-3">
                        <label for="signupEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control border-primary" id="signupEmail" name="signupEmail" placeholder="Enter your email" >
                    </div>
                    <div class="mb-3">
                        <label for="signupPassword" class="form-label">Password</label>
                        <input type="password" class="form-control border-primary" id="signupPassword" name="signupPassword" placeholder="Create a password" >
                    </div>
                    <div class="mb-3">
                        <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control border-primary" id="signupConfirmPassword" name="signupConfirmPassword" placeholder="Confirm your password" >
                    </div>
                    <!-- Hidden field to identify signup form -->
                    <input type="hidden" name="formType" value="signup">
                    <button type="submit" class="btn btn-primary w-100">Signup</button>
                </form>
            </div>

        </div>
    </div>
</div>


<?php require_once "./components/footer.php"; ?>
