<?php
require_once 'vendor/autoload.php';
require_once 'models/authModel.php';
require_once 'config/database.php';

session_start();

use League\OAuth2\Client\Provider\GenericProvider;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new Db(); 
$userModel = new Auth($db->getConnection());

$provider = new GenericProvider([
    'clientId'                => $_ENV['AZURE_CLIENT_ID'],
    'clientSecret'            => $_ENV['AZURE_CLIENT_SECRET'],
    'redirectUri'             => $_ENV['AZURE_REDIRECT_URI'],
    'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
    'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
    'scopes'                  => $_ENV['AZURE_SCOPES'],
]);

// ... existing code ...

if (!isset($_GET['code'])) {
    header('Location: views/login.php');
    exit;
}

if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

try {
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    $microsoftUser = $provider->getResourceOwner($accessToken)->toArray();
    $email = $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'];
    $name = $microsoftUser['displayName'];

} catch (Exception $e) {
    echo "Error fetching Microsoft account details: " . $e->getMessage();
    exit();
}

$bio = isset($_SESSION['tutorBio']) ? trim($_SESSION['tutorBio']) : null; 
$role_id = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;

// Check if the user already exists
$userInfo = $userModel->getUserInfo($email);
if ($userInfo) {
    // User exists, retrieve their role
    $role_id = $userInfo['role_id'];
    $userId = $userInfo['id'];

    // Check if the user is a tutor and if they are verified
    if ($role_id === 2) { // Tutor role
        $tutorDetails = $userModel->getTutorDetails($userId); // Assume this method fetches tutor info
        if ($tutorDetails && !$tutorDetails['is_verified']) {
            // Redirect to under_review if the tutor is not verified
            header('Location: views/under_review.php');
            exit();
        }
    }

    // Set session for existing user
    $_SESSION['user'] = [
        'id' => $userId,
        'name' => $name,
        'email' => $email,
        'role_id' => $role_id
    ];
} else {
    // New user registration
    if ($role_id === 2 && $bio) {
        if ($userModel->register($name, $email, '', $role_id, $microsoft_acc = true)) {
            $userInfo = $userModel->getUserInfo($email);
            $userId = $userInfo['id'];

            // Register tutor details
            $is_verified = false; // New tutors are not verified by default
            if ($userModel->registerTutor($userId, $bio, $is_verified)) {
                // Redirect to under_review if the tutor is not verified
                header('Location: views/under_review.php');
                exit();
            } else {
                echo "Failed to register tutor details!";
                exit();
            }
        } else {
            echo "Failed to register user.";
            exit();
        }
    } elseif ($role_id === 3) {
        // Role 3 (student or similar) can be handled here
        if ($userModel->register($name, $email, '', $role_id, $microsoft_acc)) {
            $userId = $userModel->getUserInfo($email);

            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id 
            ];
        } else {
            echo "Failed to register user.";
            exit();
        }
    } else {
        echo "Invalid role or missing session data.";
        exit();
    }
}

// Redirect to the courses page if everything is successful
header('Location: views/courses.php');
exit();
