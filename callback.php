<?php
require_once 'vendor/autoload.php';
require_once 'models/authModel.php';
require_once 'config/database.php';

session_start(); // Start the session

use League\OAuth2\Client\Provider\GenericProvider;

// Load environment variables
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

// Step 1: Ensure 'code' is present
if (!isset($_GET['code'])) {
    header('Location: ../views/login.php');
    exit;
}

// Step 2: Validate state to protect against CSRF
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

try {
    // Step 3: Get access token
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Step 4: Fetch user details from Microsoft
    $microsoftUser = $provider->getResourceOwner($accessToken)->toArray();
    $email = $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'];
    $name = $microsoftUser['displayName'];

} catch (Exception $e) {
    echo "Error fetching Microsoft account details: " . $e->getMessage();
    exit();
}

// Step 5: Check if session data for bio and role_id exists
$bio = isset($_SESSION['tutorBio']) ? trim($_SESSION['tutorBio']) : null; // Ensure bio is properly set
$role_id = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;

if ($role_id === 2 && $bio) {
    // Tutor Registration Flow (role_id = 2)

    // Check if the email already exists
    if ($userModel->emailExists($email)) {
        echo "Email already exists!";
        exit();
    }

    // Register tutor in the 'users' table
    if ($userModel->register($name, $email, '', $role_id, $microsoft_acc = true)) {
        $userId = $userModel->getUserInfo($email);

        // Add tutor details in the 'tutors' table
        $is_verified = false; // Waiting for admin approval
        if ($userModel->registerTutor($userId, $bio, $is_verified)) {
            echo "Tutor registration successful, pending verification!";
            // Log the user in after successful registration
            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id 
            ];
        } else {
            echo "Failed to register tutor details!";
            exit();
        }
    } else {
        echo "Failed to register user.";
        exit();
    }

    // Clear session data after successful tutor registration
    unset($_SESSION['tutorBio']);
    unset($_SESSION['role_id']);

} elseif ($role_id === 3) {
    // Normal User Registration Flow (role_id = 3)

    // Check if email already exists
    if ($userModel->emailExists($email)) {
        echo "Email already exists!";
        exit();
    }

    // Register normal user in the 'users' table
    if ($userModel->register($name, $email, '', $role_id, $microsoft_acc)) {
        // Log the user in after successful registration
        $userId = $userModel->getUserInfo($email);

        $_SESSION['user'] = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'role_id' => $role_id 
        ];
        echo "User registration successful!";
    } else {
        echo "Failed to register user.";
        exit();
    }

} else {
    // Invalid or missing role_id scenario
    echo "Invalid role or missing session data.";
    exit();
}

// Redirect after successful registration
header('Location: views/courses.php');
exit();
?>
