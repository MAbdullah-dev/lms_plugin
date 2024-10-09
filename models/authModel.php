<?php
    require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/dbHelper.php';



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


public function register($name, $email, $password = null, $role_id) {
    // Handle cases where password is not provided (OAuth users)
    $hashedPassword = $password ? password_hash($password, PASSWORD_BCRYPT) : null;

    // Prepare the query
    $query = "INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);

    if ($stmt === false) {
        die('Error in SQL prepare: ' . $this->conn->error);
    }

    // Bind parameters (for OAuth users, password will be null)
    $stmt->bind_param('sssi', $name, $email, $hashedPassword, $role_id);

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
    $query = "SELECT  id,name,email, role_id FROM users WHERE email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc(); 
}

public function registerTutor($userId, $bio) {
    $query = "INSERT INTO tutors (user_id, bio, is_verified) VALUES (?, ?, 0)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("is", $userId, $bio);
    return $stmt->execute();
}

// public function getUserIdByEmail($email) {
//     $query = "SELECT id FROM users WHERE email = ?";
//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $user = $result->fetch_assoc();
//     return $user['id'];
// }


}
?>
