<?php require_once "./components/header.php" ?>


<div class="container mt-4">
    <h2 class="text-center text-primary">Courses</h2>
    <div class="row">
        <!-- Dynamic course data: Fetch and display courses from the database -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">Course Title 1</h5>
                    <p class="card-text">This is a brief description of the course. It gives an overview of the content covered.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Capacity: 30</li>
                        <li class="list-group-item">Price: Free</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary w-100">View Course</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">Course Title 2</h5>
                    <p class="card-text">This is a brief description of the course. It gives an overview of the content covered.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Capacity: 20</li>
                        <li class="list-group-item">Price: $50</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary w-100">View Course</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">Course Title 3</h5>
                    <p class="card-text">This is a brief description of the course. It gives an overview of the content covered.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Capacity: 25</li>
                        <li class="list-group-item">Price: $30</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary w-100">View Course</a>
                </div>
            </div>
        </div>

        <!-- Add more course cards as necessary -->
    </div>
</div>

<?php require_once "./components/footer.php" ?>