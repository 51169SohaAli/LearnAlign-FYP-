<?php
session_start();
$instructor_id = isset($_SESSION['instructor_id']) ? $_SESSION['instructor_id'] : null;

if (!$instructor_id) {
    echo json_encode(['error' => 'Instructor ID not set in session']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch the latest semester_id
$semesterQuery = "SELECT MAX(semester_id) AS latest_semester_id FROM semester";
$semesterResult = $conn->query($semesterQuery);
if (!$semesterResult || $semesterResult->num_rows === 0) {
    echo json_encode(['error' => 'Failed to fetch latest semester ID']);
    exit;
}
$semesterRow = $semesterResult->fetch_assoc();
$latestSemesterId = $semesterRow['latest_semester_id'];

// Query to fetch course details along with CLOs for the instructor and latest semester
$sql = "
    SELECT 
        c.course_id, 
        c.course_name, 
        GROUP_CONCAT(cl.clo SEPARATOR ', ') AS clo
    FROM 
        course_instructor ci
    INNER JOIN 
        courses c ON ci.course_id = c.course_id
    LEFT JOIN 
        clo cl ON c.course_id = cl.course_id
    WHERE 
        ci.instructor_id = ? AND ci.semester_id = ?
    GROUP BY 
        c.course_id, c.course_name
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param('si', $instructor_id, $latestSemesterId);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = [
        'course_id' => $row['course_id'],
        'course_name' => $row['course_name'],
        'clo' => $row['clo'] ?: 'No CLOs Mapped'
    ];
}

if (empty($courses)) {
    echo json_encode(['error' => 'No courses found for the given instructor in the latest semester']);
} else {
    echo json_encode($courses);
}

$stmt->close();
$conn->close();
?>
