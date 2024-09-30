<?php
require_once '../config/database.php';
require_once '../config/dbHelper.php';

class Auth {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Check if email already exists
    public function emailExists($email) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        DBHelper::bindParams($stmt, [
            ['type' => 's', 'value' => $email]
        ]);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Register new user
    public function register($name, $email, $password, $role_id) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, 3)";
        $stmt = $this->conn->prepare($query);
        DBHelper::bindParams($stmt, [
            ['type' => 's', 'value' => $name],
            ['type' => 's', 'value' => $email],
            ['type' => 's', 'value' => $hashedPassword]
        ]);
        return $stmt->execute();
    }

    // Authenticate user during login
    public function authenticate($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        DBHelper::bindParams($stmt, [
            ['type' => 's', 'value' => $email]
        ]);
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
public function getUserInfo($email) {
    $query = "SELECT  id,name, role_id FROM users WHERE email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc(); 
}

}
?>
