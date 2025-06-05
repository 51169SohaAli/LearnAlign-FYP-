<?php
// handleCLOSelection.php

// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection setup (replace with your actual database credentials)
$host = 'localhost';  // Database host
$dbname = 'obe';  // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password

header('Content-Type: application/json'); // Set the response format as JSON

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if cloId is provided in the request
    if (isset($_GET['cloId'])) {
        $cloId = $_GET['cloId'];

        // Fetch the CLO details (name, description, weightage) from the CLO table
        $stmt = $pdo->prepare("
            SELECT clo, description, weightage
            FROM clo
            WHERE clo_id = :cloId
        ");
        $stmt->execute(['cloId' => $cloId]);

        $cloData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cloData) {
            // Fetch the total assigned weightage for this CLO from the clo_question_assignment table
            $stmtAssignedWeightage = $pdo->prepare("
                SELECT SUM(weightage) AS assigned_weightage
                FROM clo_question_assignment
                WHERE clo_id = :cloId
            ");
            $stmtAssignedWeightage->execute(['cloId' => $cloId]);
            $assignedWeightageData = $stmtAssignedWeightage->fetch(PDO::FETCH_ASSOC);
            $assignedWeightage = $assignedWeightageData['assigned_weightage'] ?? 0;

            // Calculate the remaining weightage
            $remainingWeightage = $cloData['weightage'] - $assignedWeightage;

            // Return the data as JSON
            echo json_encode([
                'success' => true,
                'cloName' => $cloData['clo'],
                'description' => $cloData['description'],
                'weightage' => $cloData['weightage'],
                'assignedWeightage' => $assignedWeightage,
                'remainingWeightage' => $remainingWeightage
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'CLO not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid CLO ID.']);
    }
} catch (PDOException $e) {
    // Return an error if something goes wrong with the database connection
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
