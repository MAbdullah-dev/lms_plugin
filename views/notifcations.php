<?php

require_once '../models/NotificationsModel.php';
$notificationModel = new NotificationModel($db->getConnection());
$notifications = $notificationModel->getNotifications($_SESSION['user']['id']);
?>

<div class="notifications">
    <h4>Notifications</h4>
    <ul>
        <?php foreach ($notifications as $notification): ?>
            <li class="<?= $notification['is_read'] ? 'read' : 'unread' ?>">
                <?= htmlspecialchars($notification['message']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    document.querySelectorAll('.notifications li').forEach(item => {
    item.addEventListener('click', function() {
        const notificationId = this.dataset.id; 
        fetch(`mark_as_read.php?id=${notificationId}`, { method: 'POST' });
        this.classList.add('read'); 
    });
});

</script>
