<?php

require_once __DIR__ . '/../models/ClassModel.php';

class ClassController {
    private $model;

    public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
        $dotenv->load();
        $this->model = new ClassModel();
    }

    public function handleCreateClass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'create_class') {
                $this->createClass();
            } elseif ($_POST['action'] === 'book_class') {
                $this->bookClass();
            }
        }
    }

    public function getClassesForCourse($courseId) {
        $courseId = filter_var($courseId, FILTER_VALIDATE_INT);
        if (!$courseId) {
            throw new Exception("Invalid course ID.");
        }

        $classes = $this->model->getClassesByCourse($courseId);

        if ($classes === false) {
            throw new Exception("Error fetching classes for the specified course.");
        }

        return [
            'success' => true,
            'data' => $classes
        ];
    }

    private function createClass() {
        if (
            isset($_GET['id'], $_SESSION['user']['id']) &&
            !empty($_POST['classTitle']) &&
            !empty($_POST['classDescription']) &&
            !empty($_POST['classCapacity']) &&
            !empty($_POST['startDate'])
        ) {
            $courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $userId = $_SESSION['user']['id'];
            $classTitle = htmlspecialchars(trim($_POST['classTitle']));
            $classDescription = htmlspecialchars(trim($_POST['classDescription']));
            $classCapacity = intval($_POST['classCapacity']);
            $classPrice = isset($_POST['classPrice']) ? floatval($_POST['classPrice']) : 0.0;
            $startDate = $_POST['startDate'];

            $token = $this->getAccessToken();

            $endDate = $this->calculateEndDate($startDate);

            $teamsLink = $this->generateTeamsMeetingLink($startDate);

            if ($teamsLink === null) {
                throw new Exception("Unable to generate Teams meeting link.");
            }

            $this->model->createClass($userId, $courseId, $classTitle, $classDescription, $teamsLink, $classCapacity, $classPrice, $startDate);

            header("Location: ../views/view_classes.php?id=" . $courseId . "&success=1");
            exit();
        } else {
            throw new Exception("Required fields are missing.");
        }
    }

private function generateTeamsMeetingLink($startDate) {
    $subject = "Class Meeting";
    $description = "This is a meeting for the class.";
    $timezone = "Asia/Karachi";

    $desiredStart = new DateTime($startDate, new DateTimeZone($timezone)); 
    $desiredEnd = clone $desiredStart;
    $desiredEnd->modify('+1 hour'); 

    $startTimeUTC = $desiredStart->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    $endTimeUTC = $desiredEnd->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');

    $meetingData = [
        "subject" => $subject,
        "startDateTime" => $startTimeUTC,
        "endDateTime" => $endTimeUTC,
        "description" => $description
    ];

    error_log("Meeting Data: " . print_r($meetingData, true));

    $token = $this->getAccessToken(); 

    try {
        return $this->sendPostRequest('https://graph.microsoft.com/v1.0/me/onlineMeetings', $meetingData, $token);
    } catch (Exception $e) {
        error_log("Error creating Teams meeting: " . $e->getMessage());
        throw new Exception("Error creating Teams meeting: " . $e->getMessage());
    }
}




public function sendPostRequest($url, $data, $token) {
    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json",
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Meeting Data sent: " . print_r($data, true));
    error_log("Response from Teams API: " . print_r(json_decode($response, true), true));

    if ($httpCode !== 201) {
        throw new Exception("API Error: " . json_decode($response, true)['error']['message']);
    }

    return json_decode($response, true);
}



    private function bookClass() {
        if (!empty($_POST['class_id']) && !empty($_POST['user_id'])) {
            $classId = intval($_POST['class_id']);
            $userId = intval($_POST['user_id']);
            $class = $this->model->getClassById($classId);

            if (!$class) {
                throw new Exception("Class not found.");
            }

            if ($class['price'] > 0) {
                header("Location: ../views/stripe_checkout.php?class_id={$classId}&user_id={$userId}");
                exit();
            }

            if ($this->model->createBooking($classId, $userId)) {
                header("Location: ../views/view_classes.php?id=" . htmlspecialchars($_GET['id']) . "&booking_success=1");
                exit();
            } else {
                throw new Exception("Error booking class.");
            }
        } else {
            throw new Exception("Class ID and User ID are required for booking.");
        }
    }

   private function getAccessToken() {
    $this->refreshAccessTokenIfNeeded();
    return $_SESSION['access_token'];
}

private function refreshAccessTokenIfNeeded() {
    if (isset($_SESSION['access_token']) && isset($_SESSION['expires_at'])) {
        if (time() >= $_SESSION['expires_at']) {
            $this->refreshAccessToken();
        }
    } else {
        $this->redirectToLogin();
    }
}

private function refreshAccessToken() {
    $clientId = $_ENV['AZURE_CLIENT_ID'];
    $clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
    $tenantId = $_ENV['AZURE_TENANT_ID'];

    $redirectUri = $_ENV['AZURE_REDIRECT_URI'];

    if (!isset($_SESSION['authorization_code'])) {
        throw new Exception("Authorization code is missing.");
    }

    $tokenUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";

    $postFields = http_build_query([
        'grant_type' => 'authorization_code',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'scope' => 'https://graph.microsoft.com/.default',
    ]);

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => $postFields,
            'ignore_errors' => true,
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($tokenUrl, false, $context);

    if ($result === FALSE) {
        throw new Exception("Error getting access token: " . print_r($http_response_header, true));
    }

    $response = json_decode($result, true);
    if (isset($response['error'])) {
        throw new Exception("Error getting access token: " . $response['error_description']);
    }

    $_SESSION['access_token'] = $response['access_token'];
    $_SESSION['expires_at'] = time() + $response['expires_in'];
}

private function redirectToLogin() {
    $clientId = $_ENV['AZURE_CLIENT_ID'];
    $tenantId = $_ENV['AZURE_TENANT_ID'];
    $redirectUri = $_ENV['AZURE_REDIRECT_URI'];

    $authUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/authorize?client_id=$clientId&response_type=code&redirect_uri=" . urlencode($redirectUri) . "&scope=" . urlencode('https://graph.microsoft.com/.default');

    header("Location: $authUrl");
    exit();
}


    private function calculateEndDate($startDate) {
        $duration = '+1 hour'; 
        $endDate = date('Y-m-d\TH:i:s', strtotime($startDate . $duration));
        return $endDate;
    }
}

