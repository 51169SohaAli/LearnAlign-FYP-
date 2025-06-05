<?php
// get_clo_id.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "obe"; 

header('Content-Type: application/json'); 

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);  

if (!$data || !isset($data['clo_id'])) {
    echo json_encode(["success" => false, "error" => "Invalid input data: Missing CLO ID."]);
    exit;
}

$cloId = intval($data['clo_id']); // Ensure CLO ID is an integer

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

try {
    // Fetch CLO details (name, description) based on CLO ID
    $query = "SELECT clo AS clo_name, description FROM clo WHERE clo_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cloId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "CLO not found in database."]);
        exit;
    }

    // Initialize variables
    $cloName = "";
    $description = "";

    // Fetch CLO name and description
    $stmt->bind_result($cloName, $description);
    if (!$stmt->fetch()) {
        echo json_encode(["success" => false, "error" => "Error fetching CLO details."]);
        exit;
    }

    $stmt->close();
    $conn->close();

    // Return CLO details
    file_put_contents("debug_log.txt", "Received CLO ID: $cloId\n", FILE_APPEND);
    echo json_encode(["success" => true, "clo_name" => $cloName, "description" => $description]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
