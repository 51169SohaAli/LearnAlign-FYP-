<?php
$servername = "localhost";
$username = "root"; // Replace 'root' with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Make sure you're using the correct database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$course_id = $_GET['course_id'];
$assessment_type = $_GET['assessment_type'];

// Get assessment_id first
$assessment_query = "SELECT assessment_id FROM assessments WHERE course_id = ? AND type = ? LIMIT 1";
$stmt = $conn->prepare($assessment_query);
$stmt->bind_param("ss", $course_id, $assessment_type);
$stmt->execute();
$result = $stmt->get_result();
$assessment = $result->fetch_assoc();

if (!$assessment) {
    echo json_encode(["error" => "Assessment not found"]);
    exit;
}

$assessment_id = $assessment['assessment_id'];

// Now get question IDs
$question_query = "SELECT question_id, question_number FROM questions WHERE assessment_id = ?";
$stmt = $conn->prepare($question_query);
$stmt->bind_param("i", $assessment_id);
$stmt->execute();
$question_result = $stmt->get_result();

$questions = [];
while ($row = $question_result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode($questions);
?>
