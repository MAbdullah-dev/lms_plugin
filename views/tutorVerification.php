<?php
require_once "./components/header.php";
require_once "../controllers/TutorController.php";

$tutorController = new TutorController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tutorId'])) {
    $tutorId = $_POST['tutorId'];
    $tutorController->verifyTutor($tutorId);
}

// Fetch all tutors
$tutors = $tutorController->getAllTutorsForAdmin();
?>

<div class="container mt-4">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Tutor Name</th>
          <th scope="col">Bio</th>
          <th scope="col">Verified</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tutors as $tutor): ?>
        <tr>
          <th scope="row"><?php echo $tutor['id']; ?></th>
          <td><?php echo $tutor['tutor_name']; ?></td>
          <td><?php echo $tutor['bio']; ?></td>
          <td><?php echo $tutor['is_verified'] ? 'Yes' : 'No'; ?></td>
          <td>
            <?php if (!$tutor['is_verified']): ?>
              <form method="POST" action="">
                <input type="hidden" name="tutorId" value="<?php echo $tutor['id']; ?>">
                <button type="submit" class="btn btn-primary">Verify</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
</div>

<?php require_once "./components/footer.php"; ?>
