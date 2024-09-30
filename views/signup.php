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
        <div id="signup">
            <form class="p-4 border rounded bg-light shadow-sm" method="POST" action="">
                <h2 class="text-center text-primary">Signup</h2>
                <div class="mb-3">
                    <label for="signupName" class="form-label">Full Name</label>
                    <input type="text" class="form-control border-primary" id="signupName" name="signupName" placeholder="Enter your full name">
                </div>
                <div class="mb-3">
                    <label for="signupEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control border-primary" id="signupEmail" name="signupEmail" placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <label for="signupPassword" class="form-label">Password</label>
                    <input type="password" class="form-control border-primary" id="signupPassword" name="signupPassword" placeholder="Create a password">
                </div>
                <div class="mb-3">
                    <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control border-primary" id="signupConfirmPassword" name="signupConfirmPassword" placeholder="Confirm your password">
                </div>
                <!-- Hidden field to identify signup form -->
                <input type="hidden" name="formType" value="signup">
                <button type="submit" class="btn btn-primary w-100">Signup</button>
            </form>
        </div>
    </div>
</div>


<?php require_once "./components/footer.php"; ?>