<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../models/NotificationsModel.php';

session_start();

if (!isset($_SESSION['user'])) {
    die("User not logged in.");
}

$db = new Db();
$notificationModel = new NotificationModel($db->getConnection());

if (isset($_POST['notification_id'])) {
    $notificationId = (int)$_POST['notification_id'];
    
    if ($notificationModel->markAsRead($notificationId)) {
        echo json_encode(['status' => 'success', 'message' => 'Notification marked as read.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to mark notification as read.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No notification ID provided.']);
}
?>
