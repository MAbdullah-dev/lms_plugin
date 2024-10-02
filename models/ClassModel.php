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

    public function getClassesByCourse($courseId) {
        $query = "SELECT * FROM classes WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        DBHelper::bindParams($stmt, [
            ['type' => 'i', 'value' => $courseId]
        ]);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createBooking($classId, $userId) {
        $status = 'available'; 
        $query = "INSERT INTO bookings (class_id, user_id, status) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("iis", $classId, $userId, $status);
        return $stmt->execute();
    }
    
    public function isClassBookedByUser($classId, $userId) {
        $query = "SELECT COUNT(*) as count FROM bookings WHERE class_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        DBHelper::bindParams($stmt, [
            ['type' => 'i', 'value' => $classId],
            ['type' => 'i', 'value' => $userId]
        ]);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0; 
    }
}
