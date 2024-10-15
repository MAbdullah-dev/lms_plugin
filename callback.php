<?php

function safeRequire2($filePath) {
    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        $fallbackPath = str_replace('../', '', $filePath);
        if (file_exists($fallbackPath)) {
            require_once $fallbackPath;
        } else {
            throw new Exception("Required file not found: " . $filePath);
        }
    }
}

try {
    safeRequire2('../vendor/autoload.php');
    safeRequire2('../models/authModel.php');
    safeRequire2('../config/database.php');
    safeRequire2('../controllers/authController.php');
    safeRequire2('../config/OAuthProviderService.php');  
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
} 


session_start();

$db = new Db(); 
$userModel = new Auth($db->getConnection());


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);      
        $dotenv->load();
$providerService = new OAuthProviderService(); 
$provider = $providerService->getProvider();  

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
        'code' => $_GET['code'],
        'scope' => $_ENV["AZURE_SCOPES"], 
    ]);

    $_SESSION['access_token'] = $accessToken->getToken();
    $_SESSION['refresh_token'] = $accessToken->getRefreshToken();
    $_SESSION['expires_at'] = $accessToken->getExpires(); 

    $microsoftUser = $provider->getResourceOwner($accessToken)->toArray();
    $email = $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'];
    $name = $microsoftUser['displayName'];

} catch (Exception $e) {
    echo "Error fetching Microsoft account details: " . $e->getMessage();
    exit();
}

$userInfo = $userModel->getUserInfo($email);

if ($userInfo) {
    $role_id = $userInfo['role_id'];
    $userId = $userInfo['id'];

    if ($role_id === 2) { 
        $tutorDetails = $userModel->getTutorDetails($userId);  
        if ($tutorDetails && !$tutorDetails['is_verified']) {
            header('Location: views/under_review.php');
            exit();
        }
    }

    $_SESSION['user'] = [
        'id' => $userId,
        'name' => $name,
        'email' => $email,
        'role_id' => $role_id,
    ];
    header('Location: views/courses.php');
    exit();

} else {
    $role_id = $_SESSION['role_id'] ?? null;
    $bio = isset($_SESSION['tutorBio']) ? trim($_SESSION['tutorBio']) : null;

    if ($role_id === 2 && $bio) {
        if ($userModel->register($name, $email, '', $role_id, $microsoft_acc = true)) {
            $userInfo = $userModel->getUserInfo($email);
            $userId = $userInfo['id'];

            if ($userModel->registerTutor($userId, $bio, $is_verified = false)) {
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
