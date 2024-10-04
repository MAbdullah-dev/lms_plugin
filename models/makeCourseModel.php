<?php
require_once "../config/database.php";

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
    public function createCourse($userId, $title, $type, $description, $visibility,  $is_paid) {
        $isPublish = 0;  

        $stmt = $this->db->prepare("INSERT INTO courses (user_id, title, type, description, visibility, is_paid, is_published) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
    
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
    
        $stmt->bind_param("isssssi", $userId, $title, $type, $description, $visibility, $is_paid, $isPublish);
    
        if ($stmt->execute()) {
            return true;
        } else {
            die("Execute failed: " . $stmt->error);  
        }
    }
    
    public function getCourses($user) {
        $roleId = $user['role_id'] ?? 0;  
        $userId = $user['id'] ?? null; 
    
        
        if ($roleId === 1) { 
            $query = "SELECT c.*, u.name AS creator_name 
                      FROM courses c 
                      JOIN users u ON c.user_id = u.id";
        } elseif ($roleId === 2) { 
            $query = "SELECT c.*, u.name AS creator_name 
                      FROM courses c 
                      JOIN users u ON c.user_id = u.id 
                      WHERE c.user_id = ?";
        } elseif ($roleId === 3) { 
            $query = "SELECT c.*, u.name AS creator_name 
                      FROM courses c 
                      JOIN users u ON c.user_id = u.id 
                      WHERE c.is_published = 1";
        } else { 
            $query = "SELECT c.*, u.name AS creator_name 
                      FROM courses c 
                      JOIN users u ON c.user_id = u.id 
                      WHERE c.is_published = 1 AND c.visibility = 'public'";
        }
    
        $stmt = $this->db->prepare($query);
        if ($roleId == 2 && $userId) {
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
            return null; 
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

   public function enrollCourse($courseId, $userId) {
    $query = "INSERT INTO enrollments (course_id, user_id) VALUES (?, ?)";
    $stmt = $this->db->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparing query: " . $this->db->error);
    }

    $stmt->bind_param("ii", $courseId, $userId);
    if ($stmt->execute()) {
        return $stmt->affected_rows > 0;
    } else {
        throw new Exception("Error executing query: " . $stmt->error);
    }
}

public function isUserEnrolled($courseId, $userId) {
    $query = "SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND user_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("ii", $courseId, $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result['count'] > 0;
}


    
}
