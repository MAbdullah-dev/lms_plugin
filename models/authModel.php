<?php
    require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/dbHelper.php';

use League\OAuth2\Client\Provider\GenericProvider;


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


public function register($name, $email, $password = null, $role_id, $microsoft_acc) {
    $hashedPassword = $password ? password_hash($password, PASSWORD_BCRYPT) : null;
    
    
    $query = "INSERT INTO users (name, email, password, role_id, microsoft_acc) VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);

    if ($stmt === false) {
        die('Error in SQL prepare: ' . $this->conn->error);
    }
    $stmt->bind_param('sssii', $name, $email, $hashedPassword, $role_id, $microsoft_acc);

    if ($stmt->execute()) {
        return $this->conn->insert_id;
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
public function getTutorDetails($userId) {
    $query = "SELECT is_verified FROM tutors WHERE user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $userId);
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

public function getEmailByMicrosoft() {
    try {
        $provider = new GenericProvider([
            'clientId'                => $_ENV['AZURE_CLIENT_ID'],
            'clientSecret'            => $_ENV['AZURE_CLIENT_SECRET'],
            'redirectUri'             => $_ENV['AZURE_REDIRECT_URI'],
            'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes'                  => 'openid profile email User.read'
        ]);

        if (!isset($_GET['code'])) {
            throw new Exception('Authorization code not found');
        }

        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        $microsoftUser = $provider->getResourceOwner($accessToken)->toArray();
        
        $email = $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'];
        
        if (!$email) {
            throw new Exception('Unable to retrieve email from Microsoft account.');
        }

        return $email;

    } catch (Exception $e) {
        echo "Error fetching Microsoft account email: " . $e->getMessage();
        return false; 
    }
}



}
?>
