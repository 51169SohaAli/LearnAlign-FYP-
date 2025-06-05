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
    $assessmentId = $_POST['assessmentId'];
    $questionId = $_POST['questionId'];
    $cloId = $_POST['cloId'];
    $weightage = $_POST['weightage'];

    $sql = "INSERT INTO questions (assessment_id, question_id, clo_id, weightage) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiis", $assessmentId, $questionId, $cloId, $weightage); // Bind parameters
        if ($stmt->execute()) {
            echo json_encode(["message" => "Question mapping saved!"]);
        } else {
            echo json_encode(["error" => "Error saving question mapping: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Error preparing query: " . $conn->error]);
    }
}

$conn->close();
?>