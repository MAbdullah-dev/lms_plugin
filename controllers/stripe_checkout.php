<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../models/ClassModel.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

if (isset($_GET['class_id']) && isset($_GET['user_id'])) {
    $classId = filter_input(INPUT_GET, 'class_id', FILTER_VALIDATE_INT);
    $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
    
    if (!$classId || !$userId) {
        die("Invalid Class ID or User ID.");
    }

    $classModel = new ClassModel();
    $class = $classModel->getClassById($classId);

    if (!$class) {
        die("Class not found.");
    }

    // Ensure 'course_id' exists in the class data
    $courseId = $class['course_id'] ?? null;
    if (!$courseId) {
        die("Course ID not found.");
    }

    $amount = $class['price'] * 100; // Convert price to cents (Stripe requires cents)

    try {
        // Create a new Stripe Checkout Session
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => htmlspecialchars($class['title']),
                    ],
                    'unit_amount' => $amount, // Price in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost/lms_plugin/controllers/stripe_success.php?session_id={CHECKOUT_SESSION_ID}&course_id=' . $courseId . '&user_id=' . $userId . '&class_id=' . $classId,
            'cancel_url' => 'http://localhost/lms_plugin/controllers/stripe_cancel.php?class_id=' . $classId,
        ]);

        // Redirect to Stripe Checkout page
        header("Location: " . $checkout_session->url);
        exit();
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle API error gracefully
        echo "Payment error: " . htmlspecialchars($e->getMessage());
        exit();
    }
} else {
    echo "Invalid request. Class ID and User ID are required.";
}
