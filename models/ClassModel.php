<?php
require_once '../config/database.php';
require_once '../config/dbHelper.php';

class ClassModel {
    private $conn;

    public function __construct() {
        $db = new Db();
        $this->conn = $db->getConnection();
    }

    public function createClass($userId, $courseId, $classTitle, $classDescription, $classLink, $classCapacity, $startDate) {
        $query = "INSERT INTO classes (course_id, user_id, title, description, link, capacity, start_date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        DBHelper::bindParams($stmt, [
            ['type' => 'i', 'value' => $courseId],
            ['type' => 'i', 'value' => $userId],
            ['type' => 's', 'value' => $classTitle],
            ['type' => 's', 'value' => $classDescription],
            ['type' => 's', 'value' => $classLink],
            ['type' => 'i', 'value' => $classCapacity],
            ['type' => 's', 'value' => $startDate]
        ]);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error inserting class: " . $stmt->error);
        }
    }
}
?>
