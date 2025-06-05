<?php

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Replace with your database name

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Extract values from the request
$assessmentId = $data['assessment_id'];
$questionNumber = $data['question_number'];
$cloId = $data['clo_id'];
$weightage = $data['weightage'];

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Save the question to the 'questions' table
    $sql = "INSERT INTO questions (assessment_id, question_number) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $assessmentId, $questionNumber); // Bind parameters
    $stmt->execute();
    $stmt->close();

    // Save the CLO weightage to the 'clo_question_assignment' table
    $sql = "INSERT INTO clo_question_assignment (assessment_id, question_number, clo_id, weightage) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $assessmentId, $questionNumber, $cloId, $weightage); // Bind parameters
    $stmt->execute();

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

?>
