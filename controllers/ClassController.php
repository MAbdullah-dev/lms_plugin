<?php
require_once '../models/ClassModel.php';

class ClassController {
    private $model;

    public function __construct() {
        $this->model = new ClassModel();
    }

    public function handleCreateClass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (isset($_GET['id']) && isset($_SESSION['user']['id']) && 
                    !empty($_POST['classTitle']) && !empty($_POST['classDescription']) && 
                    !empty($_POST['classLink']) && !empty($_POST['classCapacity']) && 
                    !empty($_POST['startDate'])) {

                    $courseId = $_GET['id'];

                    $userId = $_SESSION['user']['id'];

                    $classTitle = $_POST['classTitle'];
                    $classDescription = $_POST['classDescription'];
                    $classLink = $_POST['classLink'];
                    $classCapacity = intval($_POST['classCapacity']);
                    $startDate = $_POST['startDate'];


                    $this->model->createClass($userId, $courseId, $classTitle, $classDescription, $classLink, $classCapacity, $startDate);

                    header("Location: ../views/view_classes.php");
                    exit();

                } else {
                    throw new Exception("All fields are required.");
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
     public function getClassesForCourse($courseId) {
        try {
            $classes = $this->model->getClassesByCourse($courseId);
            return $classes;
        } catch (Exception $e) {
            echo "Error fetching classes: " . $e->getMessage();
        }
    }
}
?>
