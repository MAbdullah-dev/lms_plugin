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
    public function getAllCourses() {
        $stmt = $this->db->prepare("
            SELECT c.id, c.title, c.description, c.price, c.type ,u.name AS creator_name
            FROM courses c
            JOIN users u ON c.user_id = u.id
        ");
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); 
    }
}
