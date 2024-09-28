<?php require_once "./components/header.php"; ?>

<div class="container mt-4">
    <h2 class="text-center text-primary">Enrollments</h2>

    <!-- Table to display enrollment data -->
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th scope="col">User</th>
                <th scope="col">Class</th>
                <th scope="col">Tutor</th>
                <th scope="col">Tutor Course ID</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamic enrollment data: Fetch from database and display here -->
            <tr>
                <td>John Doe</td>
                <td>Math 101</td>
                <td>Tutor 1</td>
                <td>TC-001</td>
            </tr>
            <tr>
                <td>Jane Smith</td>
                <td>Science 101</td>
                <td>Tutor 2</td>
                <td>TC-002</td>
            </tr>
            <!-- Add more rows as necessary -->
        </tbody>
    </table>
</div>

<?php require_once "./components/footer.php"; ?>
