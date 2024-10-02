<?php
require_once "./components/header.php";
require_once "../controllers/makeCourseController.php";

$controller = new MakeCourseController();
$courses = $controller->getCoursesWithEnrollmentStatus();

// Handle course enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $controller->enrollInCourse();
}

?>

<div class="container mt-4">
    <h2 class="text-center text-primary">Courses</h2>
    <div class="row">
       <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                    <ul class="list-group list-group-flush">
                        <div class="d-flex justify-content-between align-items-center border-bottom p-2">
                            <li class="list-unstyled">Price: <?php echo ($course['price'] > 0) ? '$' . htmlspecialchars($course['price']) : 'Free'; ?></li>
                            <li class="list-unstyled text-capitalize">Course Level: <?php echo htmlspecialchars($course['type']); ?></li>
                        </div>
                        <li class="list-unstyled mt-3 text-capitalize">Created by: <?php echo htmlspecialchars($course['creator_name']); ?></li>
                    </ul>
                </div>

                <div class="card-footer">
                    <!-- Check if the user is logged in -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- If the user is role_id 3 (student) -->
                        <?php if ($_SESSION['user']['role_id'] === 3): ?>
                            <?php if ($course['is_enrolled']): ?>
                                <!-- Show View Course button if student is enrolled -->
                                <a href="./view_classes.php?id=<?php echo $course['id']; ?>" class="btn btn-primary w-100">View Course</a>
                            <?php else: ?>
                                <!-- Show Enroll button if student is not enrolled -->
                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#enrollModal-<?php echo $course['id']; ?>">Enroll</button>
                            <?php endif; ?>

                        <!-- If the user is role_id 1 (admin) or 2 (instructor), always show the View Course button -->
                        <?php elseif (in_array($_SESSION['user']['role_id'], [1, 2])): ?>
                            <a href="./view_classes.php?id=<?php echo $course['id']; ?>" class="btn btn-primary w-100">View Course</a>

                        <?php endif; ?>

                    <!-- If the user is not logged in, show View Course button -->
                    <?php else: ?>
                        <a href="./view_classes.php?id=<?php echo $course['id']; ?>" class="btn btn-secondary w-100">View Course</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Enrollment Modal -->
        <div class="modal fade" id="enrollModal-<?php echo $course['id']; ?>" tabindex="-1" aria-labelledby="enrollModalLabel-<?php echo $course['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="enrollModalLabel-<?php echo $course['id']; ?>">Confirm Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to enroll in the course "<?php echo htmlspecialchars($course['title']); ?>"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="" method="POST" class="d-inline">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>">
                            <button type="submit" name="enroll" class="btn btn-primary">Yes, Enroll</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
    </div>
</div>

<?php require_once "./components/footer.php"; ?>
