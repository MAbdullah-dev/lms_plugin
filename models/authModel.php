<?php
require_once './config/dbHelper.php'; 

class User {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Check if email already exists
    public function emailExists($email) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParams("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Register new user
    public function register($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParams("sss", $name, $email, $hashedPassword);
        return $stmt->execute();
    }

    // Authenticate user during login
    public function authenticate($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParams("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }
}
?>
