<?php
ini_set('display_errors', 0); // Do NOT display errors as HTML
ini_set('log_errors', 1);     // Log errors to server error log
error_reporting(E_ALL);       // Report all PHP errors

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "obe");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data) || empty($data)) {
    echo json_encode(["success" => false, "error" => "Invalid or no data received"]);
    exit;
}

// Assume all entries are for the same course
$courseId = $_GET['course_id'] ?? null;

file_put_contents("debug_log.txt", "Raw data: " . print_r($data, true) . "\n", FILE_APPEND);
file_put_contents("debug_log.txt", "Course ID: " . print_r($courseId, true) . "\n", FILE_APPEND);

if (!$courseId) {
    echo json_encode(["success" => false, "error" => "Missing course_id (send as GET parameter)"]);
    exit;
}

// Get valid student IDs for this course
$validStudentIds = [];
$studentQuery = $conn->prepare("SELECT student_id FROM students_course WHERE course_id = ?");
$studentQuery->bind_param("i", $courseId);
$studentQuery->execute();
$studentResult = $studentQuery->get_result();
while ($row = $studentResult->fetch_assoc()) {
    $validStudentIds[] = $row['student_id'];
}
$studentQuery->close();

// Prepare insert/update
$stmt = $conn->prepare("
    INSERT INTO student_assessments (student_id, question_id, obtained_marks)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE obtained_marks = VALUES(obtained_marks)
");

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Failed to prepare statement: " . $conn->error]);
    exit;
}

foreach ($data as $entry) {
    $studentId = $entry['student_id'] ?? null;
    $questionId = $entry['question_id'] ?? null;
    $obtainedMarks = $entry['obtained_marks'] ?? null;

    if (
        !$studentId ||
        !$questionId ||
        !is_numeric($obtainedMarks) ||
        !in_array($studentId, $validStudentIds)
    ) {
        if (!in_array($studentId, $validStudentIds)) {
            file_put_contents("debug_log.txt", "❌ Rejected student: $studentId (not in course $courseId)\n", FILE_APPEND);
        }
        continue;
    }

    file_put_contents("debug_log.txt", "✅ Inserting: student_id=$studentId, question_id=$questionId, marks=$obtainedMarks\n", FILE_APPEND);

    $stmt->bind_param("iid", $studentId, $questionId, $obtainedMarks);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "error" => "Execution failed: " . $stmt->error]);
        exit;
    }
}


$stmt->close();
$conn->close();

echo json_encode(["success" => true, "message" => "Obtained marks saved successfully"]);
?>
