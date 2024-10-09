<?php
require_once "../config/database.php";

class TutorModel {
    private $db;
    public function __construct() {
        $this->db = (new Db())->getConnection();
    }

    // Get all tutors along with their user names
    public function getAllTutors() {
        $query = "SELECT t.id, t.user_id, t.bio, t.is_verified, u.name AS tutor_name 
                  FROM tutors t
                  JOIN users u ON t.user_id = u.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $tutors = [];
        while ($tutor = $result->fetch_assoc()) {
            $tutors[] = $tutor;
        }
        return $tutors;
    }

    // Update tutor verification status
    public function verifyTutor($tutorId) {
        $query = "UPDATE tutors SET is_verified = 1 WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $tutorId);
        return $stmt->execute();
    }
}
