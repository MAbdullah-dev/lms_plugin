<?php
require_once './config/Db.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Db();
        $this->conn = $db->getConnection();
    }

    public function register($name, $email, $password) {

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        $stmt->close();
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $emailExists = $stmt->num_rows > 0;
        $stmt->close();
        return $emailExists;
    }
}
