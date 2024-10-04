<?php
if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];
    header("Location: ../views/view_classes.php?id=" . $classId . "&payment_cancelled=1");
    exit();
} else {
    echo "Invalid request.";
}
