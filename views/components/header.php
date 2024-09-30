<?php 
session_start(); // Start the session at the very top

require_once '../controllers/AuthController.php';

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $controller->logout(); // Handle the logout action
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/header.css">
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">LMS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-primary" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <?php if (isset($_SESSION['user'])): ?> <!-- Check for logged-in user -->
                                <form action="" method="POST" class="d-inline">
                                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                                </form>
                            <?php else: ?>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>
