<?php
require_once './models/authModel.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['signupName']);
            $email = trim($_POST['signupEmail']);
            $password = trim($_POST['signupPassword']);
            $confirmPassword = trim($_POST['signupConfirmPassword']);

            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                echo "All fields are required!";
                return;
            }

            if ($password !== $confirmPassword) {
                echo "Passwords do not match!";
                return;
            }

            if ($this->user->emailExists($email)) {
                echo "Email already exists!";
                return;
            }

            if ($this->user->register($name, $email, $password)) {
                echo "User registered successfully!";
            } else {
                echo "Failed to register user.";
            }
        }
    }
}
