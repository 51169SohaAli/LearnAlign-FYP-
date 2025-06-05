<?php
session_start();
$instructor_id = isset($_SESSION['instructor_id']) ? $_SESSION['instructor_id'] : null;

if (!$instructor_id) {
    echo json_encode(['error' => 'Instructor ID not set in session']);
    exit;
}

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

$sql = "SELECT course_id FROM courses WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param('s', $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row['course_id'];
}

if (empty($courses)) {
    echo json_encode(['error' => 'No courses found for the given instructor']);
} else {
    echo json_encode($courses);
}

$stmt->close();
$conn->close();
?>

