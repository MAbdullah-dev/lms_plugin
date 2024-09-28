<?php require_once "./components/header.php" ?>
<div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-4">
            <form class="p-4 border rounded bg-light shadow-sm">
                <h2 class="text-center text-primary">Login</h2>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control border-primary" id="email" placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control border-primary" id="password" placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

<?php require_once "./components/footer.php" ?>