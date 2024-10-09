<?php

require_once '../config/database.php';
class NotificationModel {
    private $db;

    public function __construct($connection) {
        $this->db = $connection;
    }

    public function createNotification($userId, $message) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function getNotifications($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($notificationId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
        $stmt->bindParam(':id', $notificationId);
        return $stmt->execute();
    }
}
