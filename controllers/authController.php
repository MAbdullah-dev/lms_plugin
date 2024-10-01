<?php
require_once '../models/authModel.php';
require_once '../config/database.php'; 



class AuthController {
    private $user;
    public $errors = []; // Use class property for errors

    public function __construct() {
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
            $email = trim($_POST['loginEmail']);
            $password = trim($_POST['loginPassword']);

            // Validate inputs
            if (empty($email) || empty($password)) {
                $this->errors[] = "Email and password are required!"; 
                return;
            }

            if ($this->user->authenticate($email, $password)) {
                $userInfo = $this->user->getUserInfo($email); 
                $_SESSION['user'] = $userInfo; 
                header("Location: ../views/courses.php");
            } else {
                $this->errors[] = "Invalid email or password!";
            }
        }
    }

    public function logout() {
        session_unset(); 
        session_destroy();
        header ("Location : ../views/login.php"); 
        exit(); 
    }
}
