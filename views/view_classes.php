<?php
// echo $_GET['id'];
require_once "./components/header.php";
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
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
    Create Class
</button>
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
                </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($classes)) {
                foreach ($classes as $class) {
                    echo "<tr>";
                    echo "<td>" . $class['id'] . "</td>";
                    echo "<td>" . $class['title'] . "</td>";
                    echo "<td><a href='" . $class['link'] . "'>" . $class['link'] . "</a></td>";
                    echo "<td>" . $class['description'] . "</td>";
                    echo "<td>" . $class['capacity'] . "</td>";
                    echo "<td>" . $class['start_date'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No classes found.</td></tr>";
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
                <h5 class="modal-title" id="createCourseModalLabel">Create Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="courseTitle" class="form-label">Class Title</label>
                        <input type="text" class="form-control" id="courseTitle" name="classTitle" placeholder="Enter course title" required>
                    </div>

                    <div class="mb-3">
                        <label for="courseDescription" class="form-label">Class Description</label>
                        <textarea class="form-control" id="courseDescription" name="classDescription" rows="3" placeholder="Enter course description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="courseLink" class="form-label">Class Link</label>
                        <input type="url" class="form-control" id="courseLink" name="classLink" placeholder="Enter course link" required>
                    </div>

                    <div class="mb-3">
                        <label for="courseCapacity" class="form-label">Class Capacity</label>
                        <input type="number" class="form-control" id="courseCapacity" name="classCapacity" placeholder="Enter course capacity" required>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="courseVisibility" class="form-label">Visibility</label>
                        <select class="form-select" id="courseVisibility" name="courseVisibility" required>
                            <option value="" disabled selected>Select visibility</option>
                            <option value="1">Visible</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div> -->

                    <div class="mb-3">
                        <label for="courseDate" class="form-label">Class Date</label>
                        <input type="datetime-local" class="form-control" id="courseDate" name="startDate" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Class</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('coursePaid').addEventListener('change', function() {
        const coursePriceField = document.getElementById('coursePriceField');
        if (this.value === 'paid') {
            coursePriceField.classList.remove('d-none');
            document.getElementById('coursePrice').setAttribute('required', 'required');
        } else {
            coursePriceField.classList.add('d-none');
            document.getElementById('coursePrice').removeAttribute('required');
        }
    });
</script>






<?php
require_once "./components/footer.php";
?>