<?php
// Set the response header to JSON
header('Content-Type: application/json');

// Get the input data
$inputData = json_decode(file_get_contents('php://input'), true);
$semester_name = $inputData['semester_name'];

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = "";     // Replace with your MySQL password
$dbname = "obe";    // Replace with your database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Check if the semester already exists
$checkStmt = $conn->prepare("SELECT semester_id FROM semester WHERE semester_name = ?");
$checkStmt->bind_param("s", $semester_name);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Semester already exists
    $checkStmt->bind_result($existing_id);
    $checkStmt->fetch();
    echo json_encode(['success' => true, 'semester_id' => $existing_id, 'message' => 'Semester already exists']);
} else {
    // Insert the new semester
    $insertStmt = $conn->prepare("INSERT INTO semester (semester_name) VALUES (?)");
    $insertStmt->bind_param("s", $semester_name);

    if ($insertStmt->execute()) {
        $semester_id = $insertStmt->insert_id;
        echo json_encode(['success' => true, 'semester_id' => $semester_id, 'message' => 'Semester inserted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert semester']);
    }
    $insertStmt->close();
}

// Close connections
$checkStmt->close();
$conn->close();
?>
