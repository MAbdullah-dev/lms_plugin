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
}
