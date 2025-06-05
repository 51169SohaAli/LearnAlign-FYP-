<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "obe"; 

header("Content-Type: application/json");

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Debugging: Log received courseId
file_put_contents("debug.log", "Received courseId: " . $_GET['courseId'] . "\n", FILE_APPEND);

// Get parameters
$course_id = isset($_GET['courseId']) ? trim($_GET['courseId']) : null;
$assessment_id = isset($_GET['assessmentId']) ? intval($_GET['assessmentId']) : null;

if (!$course_id) {
    echo json_encode(["success" => false, "error" => "Course ID is required."]);
    exit();
}

// Query to fetch all CLOs related to the selected course
$query = "
    SELECT DISTINCT c.clo_id, c.clo AS clo_name, 
           COALESCE(SUM(cqa.weightage), 0) AS assigned_weight, 
           c.weightage AS total_weight
    FROM clo c
    LEFT JOIN clo_question_assignment cqa ON c.clo_id = cqa.clo_id
    LEFT JOIN questions q ON cqa.question_id = q.question_id
    LEFT JOIN assessments a ON q.assessment_id = a.assessment_id
    WHERE c.course_id = ?
";

// Filter by assessment if provided
if ($assessment_id) {
    $query .= " AND a.assessment_id = ?";
}

// Group by CLO ID to ensure uniqueness
$query .= " GROUP BY c.clo_id, c.clo, c.weightage";

$stmt = $conn->prepare($query);
if ($assessment_id) {
    $stmt->bind_param("si", $course_id, $assessment_id);
} else {
    $stmt->bind_param("s", $course_id);
}


$stmt->execute();
$result = $stmt->get_result();

$clos = [];
while ($row = $result->fetch_assoc()) {
    $clos[] = [
        'clo_id' => $row['clo_id'],
        'name' => $row['clo_name'],
        'assignedWeight' => $row['assigned_weight'],
        'totalWeightage' => $row['total_weight']
    ];
}

// Return results
echo json_encode(["success" => true, "clos" => $clos]);

$stmt->close();
$conn->close();
?>
