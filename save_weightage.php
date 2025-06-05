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

    // Get JSON input from AJAX request
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['course_id']) || !isset($data['clo_weightages'])) {
        echo json_encode(["success" => false, "message" => "Missing required parameters"]);
        exit;
    }

    $course_id = $data['course_id'];
    $clo_weightages = $data['clo_weightages'];

    // Prepare SQL statement
    $stmt = $conn->prepare("UPDATE clo SET weightage = ? WHERE clo = ? AND course_id = ?");

    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Failed to prepare SQL statement"]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();
    $errors = [];

    try {
        foreach ($clo_weightages as $cloData) {
            $clo = $cloData['clo']; // Using `clo` as the unique identifier
            $weightage = intval($cloData['weightage']); // Ensure weightage is an integer

            $stmt->bind_param("iss", $weightage, $clo, $course_id);

            if (!$stmt->execute()) {
                $errors[] = "Failed to update CLO: $clo";
            }
        }

        // Commit or rollback transaction
        if (empty($errors)) {
            $conn->commit();
            echo json_encode(["success" => true, "message" => "Weightages saved successfully"]);
        } else {
            $conn->rollback();
            echo json_encode(["success" => false, "message" => implode(", ", $errors)]);
        }

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

?>
