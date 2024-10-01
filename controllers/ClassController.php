<?php
require_once '../models/ClassModel.php';

class ClassController {
    private $model;

    public function __construct() {
        $this->model = new ClassModel();
    }

    public function handleCreateClass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                try {
                    if ($_POST['action'] === 'create_class') {
                        $this->createClass();
                    } elseif ($_POST['action'] === 'book_class') {
                        $this->bookClass();
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
    }

    private function createClass() {
        if (
            isset($_GET['id']) && isset($_SESSION['user']['id']) &&
            !empty($_POST['classTitle']) && !empty($_POST['classDescription']) &&
            !empty($_POST['classLink']) && !empty($_POST['classCapacity']) &&
            !empty($_POST['startDate'])
        ) {
            $courseId = $_GET['id'];
            $userId = $_SESSION['user']['id'];
            $classTitle = $_POST['classTitle'];
            $classDescription = $_POST['classDescription'];
            $classLink = $_POST['classLink'];
            $classCapacity = intval($_POST['classCapacity']);
            $startDate = $_POST['startDate'];

            $this->model->createClass($userId, $courseId, $classTitle, $classDescription, $classLink, $classCapacity, $startDate);

            header("Location: ../views/view_classes.php?id=" . $courseId . "&success=1");
            exit();
        } else {
            throw new Exception("All fields are required.");
        }
    }

    private function bookClass() {
        if (!empty($_POST['class_id']) && !empty($_POST['user_id'])) {
            $classId = $_POST['class_id'];
            $userId = $_POST['user_id'];
            if ($this->model->createBooking($classId, $userId)) {
                header("Location: ../views/view_classes.php?id=" . $_GET['id'] . "&booking_success=1");
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
            $userId = $_SESSION['user']['id'];

            foreach ($classes as &$class) {
                $class['isBooked'] = $this->model->isClassBookedByUser($class['id'], $userId);
            }

            return $classes;
        } catch (Exception $e) {
            error_log("Error fetching classes: " . $e->getMessage());
            return [];
        }
    }
}
