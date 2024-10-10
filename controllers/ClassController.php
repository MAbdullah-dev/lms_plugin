<?php
require_once '../models/ClassModel.php';
class ClassController {
    private $model;
    public function __construct() {
        $this->model = new ClassModel();
    }
    public function handleCreateClass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            try {
                if ($_POST['action'] === 'create_class') {
                    $this->createClass();
                } elseif ($_POST['action'] === 'book_class') {
                    $this->bookClass();
                }
            } catch (Exception $e) {
                header("Location: ../views/create_class.php?error=" . urlencode($e->getMessage()));
                exit();
            }
        }
    }
    private function createClass() {
        if (
            isset($_GET['id'], $_SESSION['user']['id']) &&
            !empty($_POST['classTitle']) &&
            !empty($_POST['classDescription']) &&
            !empty($_POST['classLink']) &&
            !empty($_POST['classCapacity']) &&
            !empty($_POST['startDate'])
        ) {
            $courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $userId = $_SESSION['user']['id'];
            $classTitle = htmlspecialchars($_POST['classTitle']);
            $classDescription = htmlspecialchars($_POST['classDescription']);
            $classLink = htmlspecialchars($_POST['classLink']);
            $classCapacity = intval($_POST['classCapacity']);
            $classPrice = isset($_POST['classPrice']) ? floatval($_POST['classPrice']) : 0.0;
            $startDate = $_POST['startDate'];
            // Save the class to the database
            $this->model->createClass($userId, $courseId, $classTitle, $classDescription, $classLink, $classCapacity, $classPrice, $startDate);
            // If the tutor is signed in with Microsoft, create an Outlook event for the class
            if (isset($_SESSION['access_token'])) {
                $tutorAccessToken = $_SESSION['access_token'];
                $classDetails = [
                    'title' => $classTitle,
                    'description' => $classDescription,
                    'start_date' => $startDate,
                    'end_date' => $this->calculateEndDate($startDate),  // Assuming you have a method to calculate end date
                    'zoom_link' => $classLink,
                    'tutor_email' => $_SESSION['user']['email'],
                    'tutor_name' => $_SESSION['user']['name']
                ];
                // Call the method to create an Outlook event for the tutor
                $this->createOutlookEventForTutor($tutorAccessToken, $classDetails);
            }
            // Redirect to view classes page after successful creation
            header("Location: ../views/view_classes.php?id=" . $courseId . "&success=1");
            exit();
        } else {
            throw new Exception("All fields are required.");
        }
    }
    private function bookClass() {
        if (!empty($_POST['class_id']) && !empty($_POST['user_id'])) {
            $classId = intval($_POST['class_id']);
            $userId = intval($_POST['user_id']);
            // Fetch class details
            $class = $this->model->getClassById($classId);
            if (!$class) {
                throw new Exception("Class not found.");
            }
            // Handle paid classes
            if ($class['price'] > 0) {
                header("Location: ../views/stripe_checkout.php?class_id={$classId}&user_id={$userId}");
                exit();
            }
            // If class is free, proceed with booking
            if ($this->model->createBooking($classId, $userId)) {
                // Redirect to the classes page after successful booking
                header("Location: ../views/view_classes.php?id=" . htmlspecialchars($_GET['id']) . "&booking_success=1");
                exit();
            } else {
                throw new Exception("Error booking class.");
            }
            // If user signed in with Microsoft, create an Outlook event for the booked class
            if (isset($_SESSION['access_token'])) {
                $accessToken = $_SESSION['access_token'];
                $classDetails = [
                    'title' => $class['title'],
                    'description' => $class['description'],
                    'start_date' => $class['start_date'],
                    'end_date' => $this->calculateEndDate($class['start_date']),
                    'zoom_link' => $class['link']
                ];
                $this->createOutlookEventForUser($accessToken, $classDetails);
            }
        } else {
            throw new Exception("Class ID and User ID are required for booking.");
        }
    }
    // Method to create an Outlook event for the tutor after creating a class
    private function createOutlookEventForTutor($accessToken, $classDetails) {
        $url = "https://graph.microsoft.com/v1.0/me/events";
        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ];
        $eventData = [
            "subject" => $classDetails['title'],
            "body" => [
                "contentType" => "HTML",
                "content" => $classDetails['description']
            ],
            "start" => [
                "dateTime" => $classDetails['start_date'],
                "timeZone" => "UTC"
            ],
            "end" => [
                "dateTime" => $classDetails['end_date'],
                "timeZone" => "UTC"
            ],
            "location" => [
                "displayName" => "Online via Zoom",
                "locationUri" => $classDetails['zoom_link']
            ]
        ];
        $options = [
            'http' => [
                'header' => implode("\r\n", $headers),
                'method' => 'POST',
                'content' => json_encode($eventData)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
            // Handle error creating event
            echo "Error creating Outlook event";
        }
    }
    // Method to create an Outlook event for the user after booking a class
    private function createOutlookEventForUser($accessToken, $classDetails) {
        // Same logic as for the tutor but specific to user booking
        $this->createOutlookEventForTutor($accessToken, $classDetails);
    }
    // Calculate the end date based on the start date (you can modify this)
    private function calculateEndDate($startDate) {
        $duration = '+1 hour';  // You can adjust the duration based on your class logic
        $endDate = date('Y-m-d\TH:i:s', strtotime($startDate . $duration));
        return $endDate;
    }
    public function getClassesForCourse($courseId) {
        try {
            $classes = $this->model->getClassesByCourse($courseId);
            // Mark classes as booked if the user has already booked them
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                foreach ($classes as &$class) {
                    $class['isBooked'] = $this->model->isClassBookedByUser($class['id'], $userId);
                }
            } else {
                foreach ($classes as &$class) {
                    $class['isBooked'] = false;
                }
            }
            return $classes;
        } catch (Exception $e) {
            // Log any errors
            error_log("Error fetching classes: " . $e->getMessage());
            return [];
        }
    }
}