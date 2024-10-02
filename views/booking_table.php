<?php require_once "./components/header.php"; 
require_once "../auth.php";

?>

<div class="container mt-4">
    <h2 class="text-center text-primary">Bookings</h2>

    <!-- Table to display booking data -->
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th scope="col">Class</th>
                <th scope="col">User</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamic booking data: Fetch from database and display here -->
            <tr>
                <td>Math 101</td>
                <td>John Doe</td>
                <td>Confirmed</td>
            </tr>
            <tr>
                <td>Science 101</td>
                <td>Jane Smith</td>
                <td>Pending</td>
            </tr>
            <tr>
                <td>History 101</td>
                <td>Michael Johnson</td>
                <td>Cancelled</td>
            </tr>
            <!-- Add more rows as necessary -->
        </tbody>
    </table>
</div>

<?php require_once "./components/footer.php"; ?>
