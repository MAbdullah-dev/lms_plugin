<?php
require_once '../vendor/autoload.php';
require_once '../models/authModel.php';
require_once '../config/database.php';

use League\OAuth2\Client\Provider\GenericProvider;

class AuthController {
    private $user;
    public $errors = []; 

    public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
        $dotenv->load();

        if (empty($_ENV['AZURE_CLIENT_ID']) || empty($_ENV['AZURE_CLIENT_SECRET']) || empty($_ENV['AZURE_REDIRECT_URI']) || empty($_ENV['AZURE_TENANT_ID'])) {
            die("Environment variables are not set correctly.");
        }

        $db = new Db(); 
        $this->user = new Auth($db->getConnection()); 
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['signupName']);
            $email = trim($_POST['signupEmail']);
            $password = trim($_POST['signupPassword']);
            $confirmPassword = trim($_POST['signupConfirmPassword']);
            $role_id = 3;

            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                $this->errors[] = "All fields are required!"; 
                return;
            }

            if ($password !== $confirmPassword) {
                $this->errors[] = "Passwords do not match!";
                return;
            }

            if ($this->user->emailExists($email)) {
                $this->errors[] = "Email already exists!";
                return;
            }

            if ($this->user->register($name, $email, $password, $role_id)) {
                $this->errors[] = "Registration successful!";
            } else {
                $this->errors[] = "Failed to register user.";
                return;    
            }
        }
    }

public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['loginWithMicrosoft'])) {
            $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
            header('Location: ' . $this->getMicrosoftLoginUrl());
            exit();
        } else {
            $email = trim($_POST['loginEmail']);
            $password = trim($_POST['loginPassword']);

            if (empty($email) || empty($password)) {
                $this->errors[] = "Email and password are required!";
                return;
            }

            if ($this->user->authenticate($email, $password)) {
                $userInfo = $this->user->getUserInfo($email);

                $_SESSION['user'] = [
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'role_id' => $userInfo['role_id'] // Store role_id in session
                ];

                header("Location: ../views/courses.php");
                exit();
            } else {
                $this->errors[] = "Invalid email or password!";
            }
        }
    }
}


    private function getMicrosoftLoginUrl() {
        $provider = new GenericProvider([
            'clientId'                => $_ENV['AZURE_CLIENT_ID'],
            'clientSecret'            => $_ENV['AZURE_CLIENT_SECRET'],
            'redirectUri'             => $_ENV['AZURE_REDIRECT_URI'],
            'urlAuthorize'            => 'https://login.microsoftonline.com/' . $_ENV['AZURE_TENANT_ID'] . '/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/' . $_ENV['AZURE_TENANT_ID'] . '/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes'                  => 'openid profile email'
        ]);

         return $provider->getAuthorizationUrl(['state' => $_SESSION['oauth2state']]);
    }

public function azureCallback() {
    session_start(); // Ensure the session is started
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $provider = new GenericProvider([
        'clientId'                => $_ENV['AZURE_CLIENT_ID'],
        'clientSecret'            => $_ENV['AZURE_CLIENT_SECRET'],
        'redirectUri'             => $_ENV['AZURE_REDIRECT_URI'],
        'urlAuthorize'            => 'https://login.microsoftonline.com/' . $_ENV['AZURE_TENANT_ID'] . '/oauth2/v2.0/authorize',
        'urlAccessToken'          => 'https://login.microsoftonline.com/' . $_ENV['AZURE_TENANT_ID'] . '/oauth2/v2.0/token',
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
            'code' => $_GET['code']
        ]);

        $resourceOwner = $provider->getResourceOwner($accessToken);
        $user = $resourceOwner->toArray();
        $email = $user['mail'] ?? $user['userPrincipalName'];
        $name = $user['displayName'];

        // Check if the user already exists in the database
        if ($this->user->emailExists($email)) {
            // Fetch the existing user's information
            $userInfo = $this->user->getUserInfo($email);

            // Store the existing user's information in the session
            $_SESSION['user'] = [
                'name' => $userInfo['name'],
                'email' => $email,
                'role_id' => $userInfo['role_id'] // Fetch role_id from the database
            ];
        } else {
            // Register the user if they don't exist
            $role_id = 3; // Set default role_id for newly registered users
            $this->user->register($name, $email, null); // Register without password for OAuth users

            // Store the new user information in the session
            $_SESSION['user'] = [
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id // Set default role_id
            ];
        }

        // Redirect to the courses page after successful login
        header("Location: ../views/courses.php");
        exit();
    } catch (Exception $e) {
        // Handle errors during authentication
        exit('Error during authentication: ' . $e->getMessage());
    }
}





    public function logout() {
        session_unset(); 
        session_destroy();
        header ("Location: ../views/login.php"); 
        exit(); 
    }
}
