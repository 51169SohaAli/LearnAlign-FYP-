<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
file_put_contents("debug_log.txt", "Received JSON in save_clo_assignment: " . print_r($data, true), FILE_APPEND);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON input."]);
    exit;
}

$questionId = isset($data['question_id']) ? intval($data['question_id']) : null;
$cloId = isset($data['clo_id']) ? intval($data['clo_id']) : null;
$weightage = isset($data['weightage']) ? floatval($data['weightage']) : null;

if (!$questionId || !$cloId || !$weightage) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

try {
    // Step 1: Insert into clo_question_assignment
    $sql = "INSERT INTO clo_question_assignment (question_id, clo_id, weightage) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed for CLO assignment: " . $conn->error);
    }
    $stmt->bind_param("iii", $questionId, $cloId, $weightage);
    if (!$stmt->execute()) {
        throw new Exception("Execution failed for CLO assignment: " . $stmt->error);
    }
    $stmt->close();

    // Step 2: Get course_id related to the question
    $courseQuery = "
        SELECT c.course_id 
        FROM questions q
        JOIN assessments a ON q.assessment_id = a.assessment_id
        JOIN courses c ON a.course_id = c.course_id
        WHERE q.question_id = ?";
    $stmt = $conn->prepare($courseQuery);
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $courseRow = $result->fetch_assoc();

    if (!$courseRow) {
        throw new Exception("Could not find course for the given question.");
    }

    $courseId = $courseRow['course_id'];
    $stmt->close();

    // Step 3: Get all students enrolled in that course
  // Step 3: Get all students enrolled in that course
$studentQuery = "SELECT student_id FROM students_course WHERE course_id = ?";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$studentsResult = $stmt->get_result();

$enrolledStudents = [];
while ($row = $studentsResult->fetch_assoc()) {
    $enrolledStudents[] = $row['student_id'];
}
$stmt->close();
    $conn->close();

    echo json_encode(["success" => true]);
   


} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}

 file_put_contents("debug_log.txt", "Saving CLO Assignment - question_id: $questionId, clo_id: $cloId, weightage: $weightage\n", FILE_APPEND);

?>
