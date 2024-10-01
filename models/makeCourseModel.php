<?php
class MakeCourseModel {
    private $db;

    public function __construct() {
        $this->db = (new Db())->getConnection(); // Get DB connection
    }

    public function isCourseTitleUnique($title) {
        // Check if the course title already exists in the database
        $stmt = $this->db->prepare("SELECT id FROM courses WHERE title = ?");
        $stmt->bind_param("s", $title);
        $stmt->execute();
        $stmt->store_result();
        
        return $stmt->num_rows === 0; // Return true if no course with the same title
    }

    public function createCourse($userId, $title, $type, $price, $description) {
        // Prepare an SQL statement
        $stmt = $this->db->prepare("INSERT INTO courses (user_id, title, type, price, description, is_published) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        // Set is_publish to 0 by default
        $isPublish = 0;

        // Bind parameters
        $stmt->bind_param("issssi", $userId, $title, $type, $price, $description, $isPublish);

        // Execute the statement and check if it was successful
        if ($stmt->execute()) {
            return true;
        } else {
            die("Execute failed: " . $stmt->error);
        }
    }
    public function getCourses($user) {
        $roleId = $user['role_id'];  // Get role_id from session
        $userId = $user['id'];       // Get user_id from session
        
        if ($roleId == 1) {
            // Role 1: Admin - Get all courses
            $query = "SELECT c.*, u.name AS creator_name FROM courses c JOIN users u ON c.user_id = u.id";
        } elseif ($roleId == 2) {
            // Role 2: Instructor - Get only the courses created by this user
            $query = "SELECT c.*, u.name AS creator_name FROM courses c JOIN users u ON c.user_id = u.id WHERE c.user_id = ?";
        } elseif ($roleId == 3) {
            // Role 3: Reviewer - Get only published courses (ispublished = 1)
            $query = "SELECT c.*, u.name AS creator_name FROM courses c JOIN users u ON c.user_id = u.id WHERE c.is_published = 1";
        }
    
        $stmt = $this->db->prepare($query);
    
        // If the role is Instructor (role_id 2), bind the user_id
        if ($roleId == 2) {
            $stmt->bind_param("i", $userId);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $courses = [];
        while ($course = $result->fetch_assoc()) {
            $courses[] = $course;
        }
    
        return $courses;
    }
    public function getAllCourses() {
        $query = "SELECT c.*, u.name AS creator_name FROM courses c JOIN users u ON c.user_id = u.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $courses = [];
        while ($course = $result->fetch_assoc()) {
            $courses[] = $course;
        }
    
        return $courses;
    }
    
    public function updateCourseStatus($courseId, $status) {
        $stmt = $this->db->prepare("UPDATE courses SET is_published = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $courseId);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function getCourseById($courseId) {
        $query = "SELECT c.*, u.name AS creator_name FROM courses c JOIN users u ON c.user_id = u.id WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null; // Course not found
        }
    }
    public function fetchCourseById($courseId) {
        $query = "SELECT c.*, u.name AS creator_name FROM courses c 
                  JOIN users u ON c.user_id = u.id 
                  WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
    }    
    
}
