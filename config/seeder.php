<?php
require_once "database.php";
class DatabaseSeeder {
    private $conn;
    public function __construct(Db $db) {
        $this->conn = $db->getConnection();
    }
    // Function to create a table and echo a message
    public function createTable($sql, $tableName) {
        if ($this->conn->query($sql) === TRUE) {
            echo "Table `$tableName` created successfully.\n";
        } else {
            echo "Error creating table `$tableName`: " . $this->conn->error . "\n";
        }
    }
    // Method to insert data
    public function insertData($sql) {
        if ($this->conn->query($sql) === TRUE) {
            echo "Data inserted successfully.\n";
        } else {
            echo "Error inserting data: " . $this->conn->error . "\n";
        }
    }
    // Check if table exists
    public function tableExists($tableName) {
        $result = $this->conn->query("SHOW TABLES LIKE '$tableName'");
        return $result->num_rows > 0;
    }
    // Check if user exists
    public function userExists($email) {
        $result = $this->conn->query("SELECT * FROM `users` WHERE `email` = '$email'");
        return $result->num_rows > 0;
    }
    // Method to run the seeding process
    public function run() {
        // Create roles table if not exists
        if (!$this->tableExists('roles')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `roles` (
                `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(50) NOT NULL
            )", 'roles');
            // Insert roles
            $this->insertData("INSERT INTO `roles` (`name`) VALUES 
                ('admin'), 
                ('tutor'), 
                ('user')");
        }
        // Create users table if not exists
        if (!$this->tableExists('users')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `users` (
                `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(50) NOT NULL UNIQUE,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `role_id` INTEGER NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )", 'users');
        }
        // Insert admin user if not exists
        if (!$this->userExists('admin@gmail.com')) {
            $this->insertData("INSERT INTO `users` (`name`, `email`, `password`, `role_id`) VALUES 
                ('Admin User', 'admin@gmail.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 1)");
        }
    // Create courses table if not exists
        if (!$this->tableExists('courses')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `courses` (
                `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `user_id` INTEGER,
                `title` VARCHAR(255) NOT NULL,
                `type` ENUM('beginner', 'intermediate', 'advanced', 'expert') NOT NULL,
                `description` LONGTEXT,
                `visibility` ENUM('Private', 'Public') NOT NULL,
                `is_paid` ENUM('Free', 'Paid') NOT NULL,
                `is_published` BOOLEAN DEFAULT FALSE,
                FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE SET NULL
            )", 'courses');
        }
        // Create classes table if not exists
        if (!$this->tableExists('classes')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `classes` (
                `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
                `user_id` INTEGER,
                `title` VARCHAR(100) NOT NULL,
                `description` TEXT,
                `link` VARCHAR(255) NOT NULL,
                `capacity` INTEGER NOT NULL,
                `price` DECIMAL(10,2) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `start_date` DATETIME,
                `course_id` INTEGER,
                FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE SET NULL,
                FOREIGN KEY(`course_id`) REFERENCES `courses`(`id`) ON UPDATE NO ACTION ON DELETE SET NULL
            )", 'classes');
        }
        // Create bookings table if not exists
        if (!$this->tableExists('bookings')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `bookings` (
                `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
                `class_id` INTEGER NOT NULL,
                `user_id` INTEGER NOT NULL,
                `payment_amount` DECIMAL(10,2) NOT NULL,
                `transition_id` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY(`class_id`) REFERENCES `classes`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
                FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            )", 'bookings');
        }
        // Create notifications table if not exists
        if (!$this->tableExists('notifications')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `notifications` (
                `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
                `user_id` INTEGER NOT NULL,
                `class_id` INTEGER NOT NULL,
                `message` TEXT NOT NULL,
                `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
                FOREIGN KEY(`class_id`) REFERENCES `classes`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            )", 'notifications');
        }
        // Create class_reports table if not exists
        if (!$this->tableExists('class_reports')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `class_reports` (
                `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
                `class_id` INTEGER NOT NULL,
                `total_revenue` DECIMAL(10,2) DEFAULT 0,
                `total_attendance` INTEGER DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(`class_id`) REFERENCES `classes`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            )", 'class_reports');
        }
        // Create tutors table if not exists
        if (!$this->tableExists('tutors')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `tutors` (
                `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `user_id` INTEGER,
                `phone` VARCHAR(15),
                `bio` TEXT,
                FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            )", 'tutors');
        }
    
        // Create enrollments table if not exists
        if (!$this->tableExists('enrollments')) {
            $this->createTable("CREATE TABLE IF NOT EXISTS `enrollments` (
                `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
                `user_id` INTEGER NOT NULL,
                `course_id` INTEGER,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
                FOREIGN KEY(`course_id`) REFERENCES `courses`(`id`) ON UPDATE NO ACTION ON DELETE SET NULL
            )", 'enrollments');
        }
        // Insert tutors into users table if not exists
        $tutors = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password123', 'phone' => '123-456-7890', 'bio' => 'Experienced tutor in Mathematics.'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'password' => 'password123', 'phone' => '987-654-3210', 'bio' => 'Expert in Science and Chemistry.'],
            ['name' => 'Emily Johnson', 'email' => 'emily@example.com', 'password' => 'password123', 'phone' => '555-555-5555', 'bio' => 'Specializes in Literature and Arts.']
        ];
        foreach ($tutors as $tutor) {
            if (!$this->userExists($tutor['email'])) {
                $this->insertData("INSERT INTO `users` (`name`, `email`, `password`, `role_id`) VALUES 
                    ('" . $tutor['name'] . "', '" . $tutor['email'] . "', '" . password_hash($tutor['password'], PASSWORD_DEFAULT) . "', 2)");
                
                // Get the last inserted user ID
                $userId = $this->conn->insert_id;
                // Insert tutor details into tutors table
                $this->insertData("INSERT INTO `tutors` (`user_id`, `phone`, `bio`) VALUES 
                    ($userId, '" . $tutor['phone'] . "', '" . $tutor['bio'] . "')");
            }
        }
    }
}
// Usage
$db = new Db();
$seeder = new DatabaseSeeder($db);
$seeder->run();