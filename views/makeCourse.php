<?php
require_once "./components/header.php"; 
require_once "../auth.php";
require_once "../controllers/makeCourseController.php"; 

$controller = new MakeCourseController();
$errors = $controller->registerCourse();

// if($_SERVER['REQUEST_METHOD'] === 'POST') {
//     var_dump($_POST);
// }
?>

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-6">

        <form class="p-4 border rounded bg-light shadow-sm" method="POST">
            <h2 class="text-center text-primary">Register Course</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>  
            <?php endif; ?>
            <div class="mb-3">
                <label for="courseTitle" class="form-label">Course Title</label>
                <input type="text" class="form-control border-primary" id="courseTitle" name="courseTitle" placeholder="Enter course title">
            </div>
            <div class="mb-3">
                <label for="courseDescription" class="form-label">Course Description</label>
                <textarea class="form-control border-primary" id="courseDescription" name="courseDescription" rows="3" placeholder="Enter course description" ></textarea>
            </div>
            <div class="mb-3">
                <label for="courseType" class="form-label">Course Type</label>
                <select class="form-select border-primary" id="courseType" name="courseType" >
                    <option value="" disabled selected>Select course type</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="visibility" class="form-label">Is the Course public or private?</label>
                <select class="form-select border-primary" id="visibility" name="visibility" >
                    <option value="" disabled selected>Select an option</option>
                    <option value="public">public</option>
                    <option value="private">private</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="is_paid" class="form-label">Is the Course Paid?</label>
                <select class="form-select border-primary" id="is_paid" name="is_paid" >
                    <option value="" disabled selected>Select an option</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <!-- <div class="mb-3 d-none" id="coursePriceField">
                <label for="coursePrice" class="form-label">Course Price</label>
                <input type="number" class="form-control border-primary" id="coursePrice" name="coursePrice" placeholder="Enter course price" min="0" step="0.01">
            </div> -->
            <button type="submit" class="btn btn-primary w-100">Register Course</button>
        </form>
    </div>
</div>

<?php require_once "./components/footer.php"; ?>
