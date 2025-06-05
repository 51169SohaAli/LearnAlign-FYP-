<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'obe';
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get course ID
$courseId = $_GET['course_id'] ?? '';

if (!$courseId) {
    echo "Course ID not provided.";
    exit;
}

// Fetch students
$students = [];
$res = $conn->query("SELECT student_id, student_name FROM students WHERE course_id = '$courseId'");
while ($row = $res->fetch_assoc()) {
    $students[$row['student_id']] = $row['student_name'];
}

// Fetch CLOs
$clos = [];
$res = $conn->query("SELECT clo_id, clo, weightage FROM clo WHERE course_id = '$courseId'");
while ($row = $res->fetch_assoc()) {
    $clos[$row['clo_id']] = [
        'name' => $row['clo'],
        'weightage' => $row['weightage'],
        'assessments' => [] // will fill later
    ];
}

// Fetch question mappings and student marks
$sql = "
SELECT 
    cqa.clo_id, cqa.weightage AS clo_question_weightage,
    q.question_id, q.assessment_id, q.question_number, q.total_marks,
    a.assessment_type,
    sm.student_id, sm.obtained_marks
FROM clo_question_assignment cqa
JOIN questions q ON cqa.question_id = q.question_id
JOIN assessments a ON q.assessment_id = a.assessment_id
JOIN student_marks sm ON sm.question_id = q.question_id
WHERE q.course_id = '$courseId'
";

$res = $conn->query($sql);

// Organize data
$data = [];
while ($row = $res->fetch_assoc()) {
    $studentId = $row['student_id'];
    $cloId = $row['clo_id'];
    $assessmentType = $row['assessment_type'];
    $questionId = $row['question_id'];
    $obtained = $row['obtained_marks'];
    $total = $row['total_marks'];
    $weightage = $row['clo_question_weightage'];

    // Calculate obtained CLO weightage from this question
    $obtainedWeight = ($total > 0) ? ($obtained / $total) * $weightage : 0;

    // Track by student and CLO
    if (!isset($data[$studentId][$cloId])) {
        $data[$studentId][$cloId] = [
            'obtained' => 0,
            'assessments' => []
        ];
    }
    $data[$studentId][$cloId]['obtained'] += $obtainedWeight;

    // Group by assessment
    if (!isset($data[$studentId][$cloId]['assessments'][$assessmentType])) {
        $data[$studentId][$cloId]['assessments'][$assessmentType] = 0;
    }
    $data[$studentId][$cloId]['assessments'][$assessmentType] += $obtainedWeight;

    // Also store this assessment under CLOs (for header display)
    if (!isset($clos[$cloId]['assessments'][$assessmentType])) {
        $clos[$cloId]['assessments'][$assessmentType] = $weightage; // just show 1st weightage
    }
}

?>
