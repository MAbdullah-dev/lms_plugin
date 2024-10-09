<?php
require_once "./components/header.php";
require_once '../controllers/AuthController.php';

$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $controller->register();
    }
$errors = $controller->errors; 

?>
<style>
    .microsoft-btn{
        margin: 0 auto;
        border: 1px solid gainsboro;
        padding: 12px 24px;
    }
</style>
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <div id="signup" class="border rounded bg-light shadow-sm">
            <form class="p-4 " method="POST" action="">
                <h2 class="text-center text-primary">Signup</h2>
                 <?php if(!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error) { ?>
                            <li class="text-center">
                                <?php echo $error; ?>
                            </li>
                        </div>
                    <?php } ?>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="signupName" class="form-label">Full Name</label>
                    <input type="text" class="form-control border-primary" id="signupName"
                     name="signupName" placeholder="Enter your full name">
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
        <input type="hidden" name="role_id" value="3"> 
                <button type="submit" class="btn btn-primary w-100">Signup</button>
                    <button class="bsk-btn btn mt-3 microsoft-btn d-flex align-items-center justify-content-center" type="submit" name="signUpWithMicrosoft">
        <object type="image/svg+xml" data="https://s3-eu-west-1.amazonaws.com/cdn-testing.web.bas.ac.uk/scratch/bas-style-kit/ms-pictogram/ms-pictogram.svg" class="me-2" style="width: 2rem; height: 2rem;"></object> 
        Sign up with Microsoft</button>
            </form>
        
        </div>


    </div>
</div>


<?php require_once "./components/footer.php"; ?>