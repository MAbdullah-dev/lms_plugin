<?php 
require_once "./components/header.php"; 
require_once "../auth.php";
require_once "../controllers/makeCourseController.php"; 

// Get course ID from the URL
if (isset($_GET['id'])) {
    $courseId = $_GET['id'];

    // Initialize controller
    $controller = new MakeCourseController();

    // Fetch course details using the ID
    $course = $controller->getCourseById($courseId);
} else {
    exit;
}

if ($course === null) {
    echo "<p class='text-center text-danger'>Course not found!</p>";
    require_once "./components/footer.php"; 
    exit;
}
?>

<div class="container mt-4">
    <h2 class="text-center text-primary">Course Details</h2>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo htmlspecialchars($course['title']); ?></h4>
            <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Created by:</strong> <?php echo htmlspecialchars($course['creator_name']); ?></li>
                <li class="list-group-item"><strong>Price:</strong> <?php echo ($course['price'] > 0) ? '$' . htmlspecialchars($course['price']) : 'Free'; ?></li>
                <li class="list-group-item"><strong>Status:</strong> 
                    <?php 
                        if ($course['is_published'] == 0) echo 'Pending';
                        elseif ($course['is_published'] == 1) echo 'Approved';
                        elseif ($course['is_published'] == 3) echo 'Rejected';
                    ?>
                </li>
            </ul>
            <div class="mt-3">
                <a href="adminCourseView.php" class="btn btn-secondary">Back to Courses</a>
            </div>
        </div>
    </div>
</div>

<?php require_once "./components/footer.php"; ?>
