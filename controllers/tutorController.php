<?php
require_once "../models/TutorModel.php";

class TutorController {
    private $model;

    public function __construct() {
        $this->model = new TutorModel();
    }

    // Fetch all tutors
    public function getAllTutorsForAdmin() {
        return $this->model->getAllTutors();
    }

    // Verify tutor and redirect back to the same page
    public function verifyTutor($tutorId) {
        if ($this->model->verifyTutor($tutorId)) {
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
            exit();
        } else {
            echo "Failed to verify tutor.";
        }
    }
}
