<?php
require_once "../config/database.php";
require_once "../models/makeCourseModel.php";

class MakeCourseController {
    private $model;
    public $errors = [];

    public function __construct() {
        $this->model = new MakeCourseModel();
    }

    public function registerCourse() {
        // Initialize an empty errors array
        $errors = [];
        // print_r($_SESSION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve data from the form
            $title = trim($_POST['courseTitle'] ?? ''); // Default to empty string if not set
            $description = trim($_POST['courseDescription'] ?? ''); // Default to empty string if not set
            $type = $_POST['courseType'] ?? ''; // Default to empty string if not set
            $coursePaid = $_POST['coursePaid'] ?? ''; // Default to empty string if not set
            $price = ($coursePaid === 'paid') ? ($_POST['coursePrice'] ?? 0) : 0; // Default price to 0 if course is free
            $userId = $_SESSION['user']['id']; // Access the 'id' from the session array
            
            // Validation: Check required fields
            if (empty($title)) {
                $errors[] = "Course title is required.";
            }
            if (empty($description)) {
                $errors[] = "Course description is required.";
            }
            if (empty($type)) {
                $errors[] = "Course type is required.";
            }
            if (empty($coursePaid)) {
                $errors[] = "Please select if the course is free or paid.";
            }
            if ($coursePaid === 'paid' && (empty($price) || $price <= 0)) {
                $errors[] = "Please enter a valid course price for paid courses.";
            }

            // Validation: Check if course title is unique
            if (!$this->model->isCourseTitleUnique($title)) {
                $errors[] = "Course title already exists!";
            }

            // If there are no errors, proceed to register the course
            if (empty($errors)) {
                if ($this->model->createCourse($userId, $title, $type, $price, $description)) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                } else {
                    $errors[] = "Failed to register course.";
                }
            }
        }

        // Return errors array to be used in the view
        return $errors;
    }
}

