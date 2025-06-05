<?php 

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Replace with your database name

// Set the response header to JSON
header('Content-Type: application/json');

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assessment_type = $_POST['assessmenttype'];
    $totalWeightage = $_POST['totalWeightage'];
    $course_id = $_POST['course_id'];

    // Prepare SQL query
    $sql = "INSERT INTO assessments (assessment_type, weightage,course_id) VALUES (?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $assessment_type, $totalWeightage, $course_id ); // Bind parameters (string types)

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(["message" => "Assessment saved successfully!"]);
        } else {
            echo json_encode(["error" => "Error saving assessment: " . $stmt->error]);
        }

        $stmt->close(); // Close statement
    } else {
        echo json_encode(["error" => "Error preparing query: " . $conn->error]);
    }
}

$conn->close(); // Close connection
?>