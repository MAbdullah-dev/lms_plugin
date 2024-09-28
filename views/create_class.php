<?php require_once "./components/header.php"; ?>
<div class="container d-flex justify-content-center align-items-center p-2">
    <div class="col-md-6">
        <form class="p-4 border rounded bg-light shadow-sm" method="POST" action="path/to/your/processing/script.php">
            <h2 class="text-center text-primary">Create Course</h2>

            <div class="mb-3">
                <label for="courseTitle" class="form-label">Course Title</label>
                <input type="text" class="form-control border-primary" id="courseTitle" name="courseTitle" placeholder="Enter course title" required>
            </div>

            <div class="mb-3">
                <label for="courseDescription" class="form-label">Course Description</label>
                <textarea class="form-control border-primary" id="courseDescription" name="courseDescription" rows="3" placeholder="Enter course description" required></textarea>
            </div>

            <div class="mb-3">
                <label for="courseLink" class="form-label">Course Link</label>
                <input type="url" class="form-control border-primary" id="courseLink" name="courseLink" placeholder="Enter course link" required>
            </div>

            <div class="mb-3">
                <label for="courseCapacity" class="form-label">Course Capacity</label>
                <input type="number" class="form-control border-primary" id="courseCapacity" name="courseCapacity" placeholder="Enter course capacity" required>
            </div>

            <div class="mb-3">
                <label for="coursePaid" class="form-label">Is the Course Paid?</label>
                <select class="form-select border-primary" id="coursePaid" name="coursePaid" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                </select>
            </div>

            <div class="mb-3 d-none" id="coursePriceField">
                <label for="coursePrice" class="form-label">Course Price</label>
                <input type="number" class="form-control border-primary" id="coursePrice" name="coursePrice" placeholder="Enter course price" min="0" step="0.01" required>
            </div>


            <div class="mb-3">
                <label for="tutorId" class="form-label">Tutor</label>
                <select class="form-select border-primary" id="tutorId" name="tutorId" required>
                    <option value="" disabled selected>Select a tutor</option>
                    <!-- Dynamic options: fetch tutors from the database -->
                    <option value="1">Tutor 1</option>
                    <option value="2">Tutor 2</option>
                    <option value="3">Tutor 3</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="courseVisibility" class="form-label">Visibility</label>
                <select class="form-select border-primary" id="courseVisibility" name="courseVisibility" required>
                    <option value="" disabled selected>Select visibility</option>
                    <option value="1">Visible</option>
                    <option value="0">Hidden</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="courseDate" class="form-label">Course Date</label>
                <input type="date" class="form-control border-primary" id="courseDate" name="courseDate" required>
            </div>

            <div class="mb-3">
                <label for="courseSelect" class="form-label">Course</label>
                <select class="form-select border-primary" id="courseSelect" name="courseId" required>
                    <option value="" disabled selected>Select a course</option>
                    <!-- Dynamic options: fetch courses from the database -->
                    <option value="course1">Course 1</option>
                    <option value="course2">Course 2</option>
                    <option value="course3">Course 3</option>
                    <!-- Add more options as necessary -->
                </select>
            </div>
            <div class="mb-3">
                <label for="createdAt" class="form-label">Created At</label>
                <input type="datetime-local" class="form-control border-primary" id="createdAt" name="createdAt" required>
            </div>

            <div class="mb-3">
                <label for="updatedAt" class="form-label">Updated At</label>
                <input type="datetime-local" class="form-control border-primary" id="updatedAt" name="updatedAt" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create Course</button>
        </form>
    </div>
</div>
<script>
    // Show or hide course price field based on course payment type
    document.getElementById('coursePaid').addEventListener('change', function() {
        const coursePriceField = document.getElementById('coursePriceField');
        if (this.value === 'paid') {
            coursePriceField.classList.remove('d-none');
            document.getElementById('coursePrice').setAttribute('required', 'required'); // Ensure price is required
        } else {
            coursePriceField.classList.add('d-none');
            document.getElementById('coursePrice').removeAttribute('required'); // Remove required if free
        }
    });
</script>
<?php require_once "./components/footer.php"; ?>