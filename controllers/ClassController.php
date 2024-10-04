<?php
require_once '../models/ClassModel.php';

class ClassController {
    private $model;

    public function __construct() {
        $this->model = new ClassModel();
    }

    public function handleCreateClass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            try {
                if ($_POST['action'] === 'create_class') {
                    $this->createClass();
                } elseif ($_POST['action'] === 'book_class') {
                    $this->bookClass();
                }
            } catch (Exception $e) {
                header("Location: ../views/create_class.php?error=" . urlencode($e->getMessage()));
                exit();
            }
        }
    }

    private function createClass() {
        if (
            isset($_GET['id'], $_SESSION['user']['id']) &&
            !empty($_POST['classTitle']) && 
            !empty($_POST['classDescription']) &&
            !empty($_POST['classLink']) && 
            !empty($_POST['classCapacity']) &&
            !empty($_POST['startDate'])
        ) {
            $courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $userId = $_SESSION['user']['id'];
            $classTitle = htmlspecialchars($_POST['classTitle']);
            $classDescription = htmlspecialchars($_POST['classDescription']);
            $classLink = htmlspecialchars($_POST['classLink']);
            $classCapacity = intval($_POST['classCapacity']);
            $classPrice = floatval($_POST['classPrice']);
            $startDate = $_POST['startDate']; 

            $this->model->createClass($userId, $courseId, $classTitle, $classDescription, $classLink, $classCapacity, $classPrice, $startDate);

            header("Location: ../views/view_classes.php?id=" . $courseId . "&success=1");
            exit();
        } else {
            throw new Exception("All fields are required.");
        }
    }

    private function bookClass() {
        if (!empty($_POST['class_id']) && !empty($_POST['user_id'])) {
            $classId = intval($_POST['class_id']);
            $userId = intval($_POST['user_id']);
    
            // Check if it's a paid class
            $class = $this->model->getClassById($classId);
            if (!$class) {
                throw new Exception("Class not found.");
            }

            if ($class['price'] > 0) {
                // Redirect to Stripe Checkout
                header("Location: ../views/stripe_checkout.php?class_id={$classId}&user_id={$userId}");
                exit();
            }
    
            // If it's a free class, proceed with booking
            if ($this->model->createBooking($classId, $userId)) {
                header("Location: ../views/view_classes.php?id=" . htmlspecialchars($_GET['id']) . "&booking_success=1");
                exit();
            } else {
                throw new Exception("Error booking class.");
            }
        } else {
            throw new Exception("Class ID and User ID are required for booking.");
        }
    }

    public function getClassesForCourse($courseId) {
        try {
            $classes = $this->model->getClassesByCourse($courseId);
    
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                foreach ($classes as &$class) {
                    $class['isBooked'] = $this->model->isClassBookedByUser($class['id'], $userId);
                }
            } else {
                foreach ($classes as &$class) {
                    $class['isBooked'] = false; 
                }
            }
    
            return $classes;
        } catch (Exception $e) {
            // Log any errors
            error_log("Error fetching classes: " . $e->getMessage());
            return [];
        }
    }
}
