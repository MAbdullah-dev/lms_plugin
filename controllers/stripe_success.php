<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../models/ClassModel.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

if (isset($_GET['session_id']) && isset($_GET['course_id']) && isset($_GET['class_id']) && isset($_GET['user_id'])) {
    $session_id = $_GET['session_id'];
    $courseId = $_GET['course_id'];
    $classId = $_GET['class_id'];
    $userId = $_GET['user_id'];

    $classModel = new ClassModel();

    try {
        // Retrieve the session from Stripe
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        // Create the booking in the database
        $classModel->createBookingWithPayment(
            $classId, 
            $userId, 
            $session->amount_total / 100, // Convert back to dollars
            $session->payment_intent
        );

        // Redirect to the class page with the correct course ID
        header("Location: ../views/view_classes.php?id=" . $courseId . "&payment_success=1");
        exit();
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
