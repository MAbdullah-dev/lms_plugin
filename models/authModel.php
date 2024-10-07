<?php
require_once '../config/database.php';
require_once '../config/dbHelper.php';

class Auth {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

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

public function register($name, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $query = "INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, 3)";
    $stmt = $this->conn->prepare($query);

    if ($stmt === false) {
        die('Error in SQL prepare: ' . $this->conn->error);
    }

    $stmt->bind_param('sss', $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        return true;
    } else {
        die('Error in SQL execution: ' . $stmt->error);
    }
}




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
