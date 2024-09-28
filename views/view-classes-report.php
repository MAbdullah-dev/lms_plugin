<?php require_once "./components/header.php" ?>

<div class="container mt-4">
    <h2 class="text-center text-primary">Class Report</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th scope="col">Class Title</th>
                <th scope="col">Total Revenue</th>
                <th scope="col">Total Attendance</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamic data: Fetch and display class reports from the database -->
            <tr>
                <td>Class 1</td>
                <td>100000 $</td>
                <td>20</td>
            </tr>
            <tr>
                <td>Class 2</td>
                <td>500000 $</td>
                <td>15</td>
            </tr>
            <!-- Add more rows as necessary -->
        </tbody>
    </table>
</div>

<?php require_once "./components/footer.php"; ?>
