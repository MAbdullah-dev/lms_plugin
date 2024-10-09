<?php 
require_once "./components/header.php"; 
$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->register();
}
$errors = $controller->errors; 
?>

<style>
    .microsoft-btn {
        margin: 0 auto;
        border: 1px solid gainsboro;
        padding: 12px 24px;
    }
</style>

<div class="container mt-5">
    <h2 class="text-center mb-4">Tutors: Sign up only with your Microsoft account</h2>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="signupTutor.php">
        <div class="mb-3">
            <label for="tutorBio" class="form-label">Your Bio</label>
            <textarea class="form-control border-primary w-100" id="tutorBio" name="tutorBio" rows="5" cols="50" placeholder="Tell us about your experience..."></textarea>
        </div>

        <input type="hidden" name="role_id" value="2">

        <button class="bsk-btn btn mt-3 microsoft-btn d-flex align-items-center justify-content-center" type="submit" name="signUpTutorWithMicrosoft">
            <object type="image/svg+xml" data="https://s3-eu-west-1.amazonaws.com/cdn-testing.web.bas.ac.uk/scratch/bas-style-kit/ms-pictogram/ms-pictogram.svg" class="me-2" style="width: 2rem; height: 2rem;"></object> 
            Sign up using Microsoft
        </button>
    </form>
</div>

<?php require_once "./components/footer.php"; ?>
