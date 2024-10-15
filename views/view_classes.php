<?php
require_once "./components/header.php";
require_once "../controllers/ClassController.php";

$classController = new ClassController();
$classes = []; 

if (isset($_GET['id'])) {
    $courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($courseId) {
        $classesResponse = $classController->getClassesForCourse($courseId);
        
        if ($classesResponse['success']) {
            $classes = $classesResponse['data'] ?? []; 
        } else {
            echo "<p>No classes available for this course.</p>";
        }
    } else {
        echo "Invalid Course ID.";
    }
} else {
    echo "Course ID not found.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $classController->handleCreateClass(); 
    }

?>

<div class="container">
    <h1 class="text-center">View Classes</h1>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 2): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
            Create Class
        </button>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Class Title</th>
                <th scope="col">Class Link</th>
                <th scope="col">Class Description</th>
                <th scope="col">Class Capacity</th>
                <th scope="col">Class Price</th>
                <th scope="col">Class Date</th>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 3): ?>
                    <th scope="col">Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($classes)): ?>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?= htmlspecialchars($class['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($class['title'] ?? 'N/A') ?></td>
                        <td><a href="<?= htmlspecialchars($class['link'] ?? '#') ?>"><?= htmlspecialchars($class['link'] ?? 'N/A') ?></a></td>
                        <td><?= htmlspecialchars($class['description'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($class['capacity'] ?? 'N/A') ?></td>
                        <td>$<?= htmlspecialchars($class['price'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($class['start_date'] ?? 'N/A') ?></td>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 3): ?>
                            <td>
                                <?php if (isset($class['isBooked']) && $class['isBooked']): ?>
                                    <button type='button' class='btn btn-secondary' disabled>Booked</button>
                                <?php else: ?>
                                    <?php if ($class['price'] > 0): ?>
                                        <form action='../controllers/stripe_checkout.php' method='GET' style="display:inline;">
                                            <input type='hidden' name='class_id' value='<?= htmlspecialchars($class['id']) ?>'>
                                            <input type='hidden' name='user_id' value='<?= htmlspecialchars($_SESSION['user']['id']) ?>'>
                                            <button type='submit' class='btn btn-success'>Pay & Book</button>
                                        </form>
                                    <?php else: ?>
                                        <button type='button' class='btn btn-success book-btn' data-bs-toggle='modal' data-bs-target='#bookClassModal' data-class-id='<?= htmlspecialchars($class['id']) ?>'>BOOK</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No classes found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal for creating a class -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCourseModalLabel">Create Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" id="userTimezone" name="timeZone">
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
                        <label for="classCapacity" class="form-label">Class Capacity</label>
                        <input type="number" class="form-control" name="classCapacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="classPrice" class="form-label">Class Price</label>
                        <input type="number" class="form-control border-primary" id="classPrice" name="classPrice" placeholder="Enter class price" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control" name="startDate" required>
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

<!-- Modal for booking a class -->
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
                    <?php if (isset($_SESSION['user'])): ?>
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user']['id']); ?>">
                    <?php endif; ?>
                    <input type="hidden" name="action" value="book_class">
                    <p>Are you sure you want to book this class?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Book Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.book-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('class_id').value = this.getAttribute('data-class-id');
        });
    });

    document.getElementById('userTimezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>

<?php require_once "./components/footer.php"; ?>
