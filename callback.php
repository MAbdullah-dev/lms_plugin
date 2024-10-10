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

    // Store access token in the session
    $_SESSION['access_token'] = $accessToken->getToken();

    $microsoftUser = $provider->getResourceOwner($accessToken)->toArray();
    $email = $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'];
    $name = $microsoftUser['displayName'];

} catch (Exception $e) {
    echo "Error fetching Microsoft account details: " . $e->getMessage();
    exit();
}

// Check if the user already exists in the database
$userInfo = $userModel->getUserInfo($email);

if ($userInfo) {
    // Existing user, check their role
    $role_id = $userInfo['role_id'];
    $userId = $userInfo['id'];

    if ($role_id === 2) { // Tutor
        $tutorDetails = $userModel->getTutorDetails($userId);  
        if ($tutorDetails && !$tutorDetails['is_verified']) {
            // Tutor is under review, redirect to the review page
            header('Location: views/under_review.php');
            exit();
        }
    }

    // User is either a normal user or a verified tutor, log them in
    $_SESSION['user'] = [
        'id' => $userId,
        'name' => $name,
        'email' => $email,
        'role_id' => $role_id,
    ];
    header('Location: views/courses.php');
    exit();

} else {
    // User does not exist, proceed with registration

    $role_id = $_SESSION['role_id'] ?? null;
    $bio = isset($_SESSION['tutorBio']) ? trim($_SESSION['tutorBio']) : null;

    if ($role_id === 2 && $bio) {
        // Register a tutor
        if ($userModel->register($name, $email, '', $role_id, $microsoft_acc = true)) {
            $userInfo = $userModel->getUserInfo($email);
            $userId = $userInfo['id'];

            // Register the tutor's bio with verification set to false
            if ($userModel->registerTutor($userId, $bio, $is_verified = false)) {
                // Redirect to under_review.php
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
        // Register a normal user
        if ($userModel->register($name, $email, '', $role_id, $microsoft_acc = true)) {
            $userInfo = $userModel->getUserInfo($email);

            $_SESSION['user'] = [
                'id' => $userInfo['id'],
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id,
            ];

            header('Location: views/courses.php');
            exit();
        } else {
            echo "Failed to register user.";
            exit();
        }
    } else {
        echo "Invalid role or missing session data.";
        exit();
    }
}
