<?php
require_once '../config/database.php';
require_once '../config/dbHelper.php';

class CourseModel
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function createCourse($title, $description, $link, $capacity, $price, $visibility, $date)
    {
        $query = "INSERT INTO courses (title, description, link, capacity, price, visibility, course_date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssiisi', $title, $description, $link, $capacity, $price, $visibility, $date);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
