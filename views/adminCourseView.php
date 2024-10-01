<?php 
require_once "./components/header.php"; 
require_once "../controllers/makeCourseController.php"; 

$controller = new MakeCourseController();
$courses = $controller->getAllCoursesForAdmin(); // Fetch all courses for admin

// Handle course approval or rejection if a button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $controller->approveCourse($_POST['course_id']);
    } elseif (isset($_POST['reject'])) {
        $controller->rejectCourse($_POST['course_id']);
    }

    // Refresh the page after action
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<div class="container mt-4">
    <h2 class="text-center text-primary">All Courses</h2>
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Course Title</th>
                <th scope="col">Created by</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $index => $course): ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1; ?></th>
                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                        <td><?php echo htmlspecialchars($course['creator_name']); ?></td>
                        <td><?php echo ($course['price'] > 0) ? '$' . htmlspecialchars($course['price']) : 'Free'; ?></td>
                        <td>
                            <?php 
                                if ($course['is_published'] == 0) echo 'Pending';
                                elseif ($course['is_published'] == 1) echo 'Approved';
                                elseif ($course['is_published'] == 3) echo 'Rejected';
                            ?>
                        </td>
                        <td>
                            <a href="./viewCourse.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">View</a>
                            <?php if ($course['is_published'] == 0): // Only show buttons if course is pending ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No courses available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once "./components/footer.php"; ?>
