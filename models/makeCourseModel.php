<?php
class MakeCourseModel {
    private $db;

    public function __construct() {
        $this->db = (new Db())->getConnection(); 
    }

    public function isCourseTitleUnique($title) {
        $stmt = $this->db->prepare("SELECT id FROM courses WHERE title = ?");
        $stmt->bind_param("s", $title);
        $stmt->execute();
        $stmt->store_result();
        
        return $stmt->num_rows === 0; 
    }

    public function createCourse($userId, $title, $type, $price, $description) {
        $stmt = $this->db->prepare("INSERT INTO courses (user_id, title, type, price, description, is_published) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }

        $isPublish = 0;

        $stmt->bind_param("issssi", $userId, $title, $type, $price, $description, $isPublish);

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
        // Prepare SQL statement to get all courses with creator information
        $stmt = $this->db->prepare("
            SELECT c.id, c.title, c.description, c.price, c.type ,u.name AS creator_name
            FROM courses c
            JOIN users u ON c.user_id = u.id
        ");
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch all courses as an associative array
    }
}
