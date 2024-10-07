<?php 
require_once "./components/header.php";
require_once '../controllers/AuthController.php';



$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
}
$errors = $controller->errors; 
?>

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <form class="p-4 border rounded bg-light shadow-sm" method="POST">
            <h2 class="text-center text-primary">Login</h2>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) { ?>
                        <li class="text-center"><?php echo $error; ?></li>
                    <?php } ?>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control border-primary" id="email" 
                name="loginEmail" placeholder="Enter your email" >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control border-primary" id="password"
                name="loginPassword" placeholder="Enter your password" >
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <hr class="my-4">
            <button type="submit" name="loginWithMicrosoft" class="btn btn-success w-100">Login with Microsoft</button>
        </form>
    </div>
</div>

<?php require_once "./components/footer.php"; ?>
