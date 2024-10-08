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
   'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
    'scopes'                  => 'openid profile email User.Read'
]);

if (!isset($_GET['code'])) {
    header('Location: ../views/login.php');
    exit;
}

if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

try {
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
        'redirect_uri' => $_ENV['AZURE_REDIRECT_URI'], 
    ]);

    $resourceOwner = $provider->getResourceOwner($accessToken);
    $user = $resourceOwner->toArray();

    $email = $user['mail'] ?? $user['userPrincipalName'];
    $name = $user['displayName'];

    if ($userModel->emailExists($email)) {
        $userInfo = $userModel->getUserInfo($email);
        $_SESSION['user'] = [
            'id' => $userInfo['id'],
            'name' => $userInfo['name'],
            'email' => $userInfo['email'],
            'role_id' => $userInfo['role_id'] 
        ];
    } else {
        if ($userModel->register($name, $email, null)) {
            $userInfo = $userModel->getUserInfo($email);
            $_SESSION['user'] = [
                'id' => $userInfo['id'],
                'name' => $userInfo['name'],
                'email' => $userInfo['email'],
                'role_id' => $userInfo['role_id'] 
            ];
        } else {
            exit("Error: Failed to register user with email: $email");
        }
    }

    header("Location: views/courses.php");
    exit();

} catch (Exception $e) {
    exit('Error during authentication: ' . $e->getMessage());
}
