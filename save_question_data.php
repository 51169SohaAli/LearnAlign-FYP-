<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
file_put_contents("debug_question.txt", "Received: " . json_encode($data) . "\n", FILE_APPEND);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON input."]);
    exit;
}

$assessmentId = isset($data['assessment_id']) ? intval($data['assessment_id']) : null;
$questionNumber = isset($data['question_number']) ? intval($data['question_number']) : null;
$questionMarks = isset($data['question_marks']) ? intval($data['question_marks']) : null;

if ($assessmentId === null || $questionNumber === null || !isset($data['question_marks'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->autocommit(true); // 👈 add this line


if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

try {
    // Check if the question already exists
    $checkSql = "SELECT question_id FROM questions WHERE assessment_id = ? AND question_number = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $assessmentId, $questionNumber);
    $checkStmt->execute();
    $checkStmt->bind_result($existingId);

    if ($checkStmt->fetch()) {
        // Already exists
        file_put_contents("debug_question.txt", "Question already exists: ID = $existingId\n", FILE_APPEND);
        echo json_encode(["success" => true, "question_id" => $existingId]);
        $checkStmt->close();
        $conn->close();
        exit;
    }

    $checkStmt->close();

    // Insert if not already present
    $sql = "INSERT INTO questions (assessment_id, question_number, question_marks) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iii", $assessmentId, $questionNumber, $questionMarks);

    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    $questionId = $conn->insert_id;
// Add this:
file_put_contents("debug_question.txt", "CHECK IN DB: SELECT * FROM questions WHERE question_id = $questionId\n", FILE_APPEND);

    $stmt->close();
    $conn->close();

    echo json_encode(["success" => true, "question_id" => $questionId]);

} catch (Exception $e) {
    file_put_contents("debug_question.txt", "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}
?>
