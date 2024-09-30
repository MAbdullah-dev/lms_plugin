<?php
require_once './models/authModel.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    // Register logic
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['signupName']);
            $email = trim($_POST['signupEmail']);
            $password = trim($_POST['signupPassword']);
            $confirmPassword = trim($_POST['signupConfirmPassword']);

            // Validate inputs
            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                echo "All fields are required!";
                return;
            }

            if ($password !== $confirmPassword) {
                echo "Passwords do not match!";
                return;
            }

            // Check if email already exists
            if ($this->user->emailExists($email)) {
                echo "Email already exists!";
                return;
            }

            // Attempt to register the user
            if ($this->user->register($name, $email, $password)) {
                echo "User registered successfully!";
            } else {
                echo "Failed to register user.";
            }
        }
    }

    // Login logic
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['loginEmail']);
            $password = trim($_POST['loginPassword']);

            // Validate inputs
            if (empty($email) || empty($password)) {
                echo "Email and password are required!";
                return;
            }

            // Attempt to authenticate the user
            if ($this->user->authenticate($email, $password)) {
                echo "Login successful!";
                // Redirect or set session here
                // For example: $_SESSION['user'] = $userData;
            } else {
                echo "Invalid email or password!";
            }
        }
    }
}
?>
