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
            if (isset($_POST['signUpTutorWithMicrosoft'])) {
                $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
                $bio = trim($_POST['tutorBio']);
                $role_id = 2; // Tutor role ID

                if (empty($bio)) {
                    $this->errors[] = "Bio is required!";
                    return;
                }

                $_SESSION['tutorBio'] = $bio;
                $_SESSION['role_id'] = $role_id;

                header('Location: ' . $this->getMicrosoftLoginUrl());
                exit();
            } else { // Normal registration flow (for normal users)
                if (isset($_POST['signUpWithMicrosoft'])) {
                    $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
                    $role_id = 3; // Normal user role ID
                    $_SESSION['role_id'] = $role_id;
                    header('Location: ' . $this->getMicrosoftLoginUrl());
                    exit();
                }

                $name = trim($_POST['signupName']);
                $email = trim($_POST['signupEmail']);
                $password = trim($_POST['signupPassword']);
                $confirmPassword = trim($_POST['signupConfirmPassword']);
                $role_id = 3; // Normal user role ID

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
                }
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
                        'id' => $userInfo['id'],
                        'name' => $userInfo['name'],
                        'email' => $userInfo['email'],
                        'role_id' => $userInfo['role_id'] 
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
            'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes'                  => 'openid profile email User.read'
        ]);

        return $provider->getAuthorizationUrl(['state' => $_SESSION['oauth2state']]);
    }
    
    public function logout() {
        session_unset(); 
        session_destroy();
        header("Location: ../views/login.php"); 
        exit(); 
    }
}
