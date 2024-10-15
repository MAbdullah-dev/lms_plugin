<?php

use League\OAuth2\Client\Provider\GenericProvider;
function safeRequire($filePath) {
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
    safeRequire('../vendor/autoload.php');
    safeRequire('../models/authModel.php');
    safeRequire('../config/database.php');
    safeRequire('../config/OAuthProviderService.php');  
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}   


class AuthController {
    private $user;
    private $oauthProviderService;
    public $errors = [];

    public function __construct() {
        $db = new Db(); 
        $this->user = new Auth($db->getConnection()); 
        $this->oauthProviderService = new OAuthProviderService();
    }


 public function getProvider() {
        return $this->oauthProviderService->getProvider(); 
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['signUpTutorWithMicrosoft'])) {
                $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
                $bio = trim($_POST['tutorBio']);
                $role_id = 2; 

                if (empty($bio)) {
                    $this->errors[] = "Bio is required!";
                    return;
                }

                $email = $this->user->getEmailByMicrosoft(); 
                if ($this->user->emailExists($email)) {
                    $this->errors[] = "An account with this email already exists!";
                    return;
                }

                $_SESSION['tutorBio'] = $bio;
                $_SESSION['role_id'] = $role_id;

                header('Location: ' . $this->getMicrosoftLoginUrl());
                exit();
            }

            if (isset($_POST['signUpWithMicrosoft'])) {
                $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
                $role_id = 3; 
                $_SESSION['role_id'] = $role_id;
                header('Location: ' . $this->getMicrosoftLoginUrl());
                exit();
            }

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

            if ($this->user->register($name, $email, $password, $role_id, false)) {
                $this->errors[] = "Registration successful!";
            } else {
                $this->errors[] = "Failed to register user.";
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
        $provider = $this->getProvider(); 
        return $provider->getAuthorizationUrl(['state' => $_SESSION['oauth2state']]);
    }

    public function logout() {
        session_unset(); 
        session_destroy();
        header("Location: ../views/login.php"); 
        exit(); 
    }
}
