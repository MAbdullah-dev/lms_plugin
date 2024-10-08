<?php require_once "./components/header.php"; ?>
<style>
    .microsoft-btn{
        margin: 0 auto;
        border: 1px solid gainsboro;
        padding: 12px 24px;
    }
</style>
<div class="container mt-5">
    <h2 class="text-center mb-4">Tutors: Sign up only with your Microsoft account</h2>
    <div class="d-flex justify-content-center">
    <button class="bsk-btn btn mt-3 microsoft-btn d-flex align-items-center justify-content-center" type="submit" name="loginWithMicrosoft">
        <object type="image/svg+xml" data="https://s3-eu-west-1.amazonaws.com/cdn-testing.web.bas.ac.uk/scratch/bas-style-kit/ms-pictogram/ms-pictogram.svg" class="me-2" style="width: 2rem; height: 2rem;"></object> 
          Sign up with Microsoft</button>
    </div>
</div>
<?php require_once "./components/footer.php"; ?>
