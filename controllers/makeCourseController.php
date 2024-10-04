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
            $is_paid = $_POST['is_paid'] ?? ''; 
            $visibility = $_POST['visibility'] ?? 'private'; 
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
            if (empty($is_paid)) {
                $errors[] = "Please select if the course is free or paid.";
            }
    
            if (!$this->model->isCourseTitleUnique($title)) {
                $errors[] = "Course title already exists!";
            }
    
            if (empty($errors)) {
                if ($this->model->createCourse($userId, $title, $type, $description, $visibility, $is_paid)) {
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
        if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        } else {
            $user = null;
        }

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

  public function enrollInCourse() {
    if (isset($_POST['course_id']) && isset($_POST['user_id'])) {
        $courseId = $_POST['course_id'];
        $userId = $_POST['user_id'];

        try {
            $success = $this->model->enrollCourse($courseId, $userId);

            if ($success) {
                $_SESSION['message'] = "You have successfully enrolled in the course!";
            } else {
                $_SESSION['message'] = "Enrollment failed. Please try again.";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['message'] = "Invalid request. Missing course ID or user ID.";
    }

    header("Location: ./view_classes.php?id=" . $courseId);
    exit();
}

public function getCoursesWithEnrollmentStatus() {
    $courses = $this->getCourses(); 
    if (isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['id']; 
    }
    else{
        $userId = null;
    }

    foreach ($courses as &$course) {
        $course['is_enrolled'] = $this->model->isUserEnrolled($course['id'], $userId); 
    }

    return $courses;
}



}

