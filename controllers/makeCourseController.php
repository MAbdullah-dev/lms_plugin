<?php
require_once "../models/makeCourseModel.php";

class MakeCourseController {
    private $model;
    public $errors = [];

    public function __construct() {
        $this->model = new MakeCourseModel();
    }

    public function registerCourse() {

        $errors = [];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['courseTitle'] ?? ''); 
            $description = trim($_POST['courseDescription'] ?? ''); 
            $type = $_POST['courseType'] ?? ''; 
            $coursePaid = $_POST['coursePaid'] ?? ''; 
            $price = ($coursePaid === 'paid') ? ($_POST['coursePrice'] ?? 0) : 0; 
            $userId = $_SESSION['user']['id']; 
            
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

         
            if (!$this->model->isCourseTitleUnique($title)) {
                $errors[] = "Course title already exists!";
            }

   
            if (empty($errors)) {
                if ($this->model->createCourse($userId, $title, $type, $price, $description)) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                } else {
                    $errors[] = "Failed to register course.";
                }
            }
        }

        return $errors;
    }
       public function getCourses() {
        $user = $_SESSION['user']; 

        // Fetch the courses based on the user's role
        return $this->model->getCourses($user);
    }
    public function getAllCoursesForAdmin() {
        $user = $_SESSION['user']; 
        
        if ($user['role_id'] == 1) { 
            return $this->model->getAllCourses();
        }

        return [];
    }

    public function approveCourse($courseId) {
        return $this->model->updateCourseStatus($courseId, 1); 
    }

    public function rejectCourse($courseId) {
        return $this->model->updateCourseStatus($courseId, 3);
    }
    public function getCourseById($courseId) {
        return $this->model->fetchCourseById($courseId);
    }
}

