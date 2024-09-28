<?php require_once "./components/header.php" ?>
<div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-4">
            <form class="p-4 border rounded bg-light shadow-sm">
                <h2 class="text-center text-primary">Register Course</h2>
                <div class="mb-3">
                    <label for="courseTitle" class="form-label">Course Title</label>
                    <input type="text" class="form-control border-primary" id="courseTitle" placeholder="Enter course title" required>
                </div>
                <div class="mb-3">
                    <label for="courseDescription" class="form-label">Course Description</label>
                    <textarea class="form-control border-primary" id="courseDescription" rows="3" placeholder="Enter course description" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="courseType" class="form-label">Course Type</label>
                    <select class="form-select border-primary" id="courseType" required>
                        <option value="" disabled selected>Select course type</option>
                        <option value="in-person">In-person</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register Course</button>
            </form>
        </div>
    </div>
<?php require_once "./components/footer.php" ?>
