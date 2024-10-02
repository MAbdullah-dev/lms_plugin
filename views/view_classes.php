<?php
require_once "./components/header.php";
require_once "../auth.php";
require_once "../controllers/ClassController.php";

$classController = new ClassController();
$classController->handleCreateClass();

if (isset($_GET['id'])) {
    $courseId = $_GET['id'];
    $classes = $classController->getClassesForCourse($courseId);
} else {
    echo "Course ID not found.";
}
?>

<div class="container">
    <h1 class="text-center">View Classes</h1>
  <?php if($_SESSION['user']['role_id'] === 1 || $_SESSION['user']['role_id'] === 2) : ?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
        Create Class
    </button>
<?php endif ?>

    <div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Class Title</th>
                    <th scope="col">Class Link</th>
                    <th scope="col">Class Description</th>
                    <th scope="col">Class Capacity</th>
                    <th scope="col">Class Date</th>
  <?php if($_SESSION['user']['role_id'] === 3) : ?>
                    <th scope="col">Action</th>
  <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($classes)) {
                    foreach ($classes as $class) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($class['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($class['title']) . "</td>";
                        echo "<td><a href='" . htmlspecialchars($class['link']) . "'>" . htmlspecialchars($class['link']) . "</a></td>";
                        echo "<td>" . htmlspecialchars($class['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($class['capacity']) . "</td>";
                        echo "<td>" . htmlspecialchars($class['start_date']) . "</td>";

                        // Check if the class is already booked
   if($_SESSION['user']['role_id'] === 3) {
                        if ($class['isBooked']) {
                            echo "<td><button type='button' class='btn btn-secondary' disabled>Booked</button></td>";
                        } else {
                            echo "<td><button type='button' class='btn btn-success book-btn' data-bs-toggle='modal' data-bs-target='#bookClassModal' data-class-id='" . htmlspecialchars($class['id']) . "'>BOOK</button></td>";
                        }
                    }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No classes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCourseModalLabel">Create Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="classTitle" class="form-label">Class Title</label>
                        <input type="text" class="form-control" name="classTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="classDescription" class="form-label">Class Description</label>
                        <textarea class="form-control" name="classDescription" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="classLink" class="form-label">Class Link</label>
                        <input type="url" class="form-control" name="classLink" required>
                    </div>
                    <div class="mb-3">
                        <label for="classCapacity" class="form-label">Class Capacity</label>
                        <input type="number" class="form-control" name="classCapacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="startDate" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="action" value="create_class">Create Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bookClassModal" tabindex="-1" aria-labelledby="bookClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookClassModalLabel">Book Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="class_id" id="class_id">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>">
                    <p>Are you sure you want to book this class?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="action" value="book_class">Book Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.book-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('class_id').value = this.getAttribute('data-class-id');
        });
    });
</script>

<?php require_once "./components/footer.php"; ?>
