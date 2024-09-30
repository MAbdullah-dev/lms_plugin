<?php
require_once "./components/header.php";
require_once "../controllers/makeCourseController.php";

$controller = new MakeCourseController();
$courses = $controller->getCourses(); // Fetch all courses
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
                                <li class="list-unstyled ">Price: <?php echo ($course['price'] > 0) ? '$' . htmlspecialchars($course['price']) : 'Free'; ?></li>
                                <li class="list-unstyled text-capitalize">Course Level: <?php echo htmlspecialchars($course['type']); ?></li>
                            </div>
                            <li class="list-unstyled mt-3 text-capitalize">Created by: <?php echo htmlspecialchars($course['creator_name']); ?></li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="./view_classes.php?id=<?php echo $course['id']; ?>" class="btn btn-primary w-100">View Course</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once "./components/footer.php"; ?>