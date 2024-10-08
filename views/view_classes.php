<?php
require_once "./components/header.php";
require_once "../controllers/ClassController.php";

$classController = new ClassController();

if (isset($_GET['id'])) {
    $courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($courseId) {
        $classes = $classController->getClassesForCourse($courseId);
    } else {
        echo "Invalid Course ID.";
    }
} else {
    echo "Course ID not found.";
}

// Handle the form submission for class creation and booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for the hidden input field for creating a class
    if (isset($_POST['action']) && $_POST['action'] === 'create_class') {
        $classController->handleCreateClass();
    }

    // Check for booking a free class
    if (isset($_POST['action']) && $_POST['action'] === 'book_class') {
        $classController->handleCreateClass(); // This will handle booking as well
    }
}
?>

<div class="container">
    <h1 class="text-center">View Classes</h1>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 2) : ?>
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
                    <th scope="col">Class Price</th>
                    <th scope="col">Class Date</th>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 3) : ?>
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
                        echo "<td>$" . htmlspecialchars($class['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($class['start_date']) . "</td>";

                        if (isset($_SESSION['user'])) {
                            if ($class['isBooked']) {
                                echo "<td><button type='button' class='btn btn-secondary' disabled>Booked</button></td>";
                            } else {
                                if ($_SESSION['user']['role_id'] === 3) {
                                    if ($class['price'] > 0) {
                                        // Paid class: Redirect to Stripe checkout
                                        echo "<td>
                                                <form action='../controllers/stripe_checkout.php' method='GET'>
                                                    <input type='hidden' name='class_id' value='" . htmlspecialchars($class['id']) . "'>
                                                    <input type='hidden' name='user_id' value='" . htmlspecialchars($_SESSION['user']['id']) . "'>
                                                    <button type='submit' class='btn btn-success'>Pay & Book</button>
                                                </form>
                                              </td>";
                                    } else {
                                        // Free class: Open booking modal
                                        echo "<td>
                                                <button type='button' class='btn btn-success book-btn' data-bs-toggle='modal' data-bs-target='#bookClassModal' data-class-id='" . htmlspecialchars($class['id']) . "'>BOOK</button>
                                              </td>";
                                    }
                                }
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

<!-- Modal for creating a class -->
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
                    <div class="mb-3" id="coursePriceField">
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
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user']['id']); ?>">
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
</script>

<?php require_once "./components/footer.php"; ?>
