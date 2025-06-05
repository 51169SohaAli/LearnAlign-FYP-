<?php
header('Content-Type: application/json');

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Get course code from GET request
$course_code = isset($_GET['course_code']) ? trim($_GET['course_code']) : '';

// Validate course code
if (empty($course_code)) {
    http_response_code(400);
    echo json_encode(["error" => "No course code provided"]);
    exit;
}

// Prepare SQL to get the latest semester for the course
$latest_semester_sql = "
    SELECT MAX(s.semester_id) AS latest_semester_id
    FROM course_instructor ci
    JOIN semester s ON ci.semester_id = s.semester_id
    JOIN courses c ON ci.course_id = c.course_id
    WHERE c.course_id = ?
";

$latest_semester_stmt = $conn->prepare($latest_semester_sql);
if (!$latest_semester_stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare SQL statement for latest semester: " . $conn->error]);
    $conn->close();
    exit;
}

$latest_semester_stmt->bind_param("s", $course_code);
if (!$latest_semester_stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to execute query for latest semester: " . $latest_semester_stmt->error]);
    $latest_semester_stmt->close();
    $conn->close();
    exit;
}

$latest_semester_result = $latest_semester_stmt->get_result();
$latest_semester_row = $latest_semester_result->fetch_assoc();
$latest_semester_id = $latest_semester_row['latest_semester_id'];

// Now, fetch the students studying the course in the latest semester
$sql = "
    SELECT sc.student_id, s.student_name, sc.course_id
    FROM students_course sc
    JOIN students s ON sc.student_id = s.student_id
    JOIN course_instructor ci ON sc.course_id = ci.course_id
    WHERE sc.course_id = ?
    AND ci.semester_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare SQL statement: " . $conn->error]);
    $conn->close();
    exit;
}

// Bind and execute
$stmt->bind_param("si", $course_code, $latest_semester_id);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to execute query: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

// Fetch results
$result = $stmt->get_result();
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Return result as JSON
echo json_encode($students);

// Close resources
$stmt->close();
$conn->close();
?>
