<?php
$servername = "localhost";
$username = "root"; // Replace 'root' with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Make sure you're using the correct database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

if (isset($_GET['course_id'])) {
    $courseID = $_GET['course_id'];

    $query = "SELECT course_name FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $courseID);
    $stmt->execute();
    $stmt->bind_result($courseName);
    
    if ($stmt->fetch()) {
        echo json_encode(["success" => true, "course_name" => $courseName]);
    } else {
        echo json_encode(["success" => false, "message" => "Course not found"]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No course ID provided"]);
}
?>
