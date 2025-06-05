<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
session_start();
$student_id = $_SESSION['student_id'];

$conn = new mysqli("localhost", "root", "", "obe");
if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$sql = "
SELECT 
    c.course_name,
    ROUND(SUM((sm.obtained_marks / q.question_marks) * cqa.weightage), 2) AS obtained_weightage,
    SUM(cqa.weightage) AS total_weightage
FROM students_course sca
JOIN courses c ON sca.course_id = c.course_id
LEFT JOIN clo cl ON cl.course_id = c.course_id
LEFT JOIN clo_question_assignment cqa ON cqa.clo_id = cl.clo_id
LEFT JOIN questions q ON q.question_id = cqa.question_id
LEFT JOIN assessments a ON a.assessment_id = q.assessment_id AND a.course_id = c.course_id
LEFT JOIN student_assessments sm ON sm.question_id = q.question_id AND sm.student_id = sca.student_id
WHERE sca.student_id = ?
GROUP BY c.course_id, c.course_name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $overall_percentage = 0;
    if (!is_null($row['obtained_weightage']) && $row['total_weightage'] > 0) {
        $overall_percentage = round(($row['obtained_weightage'] / $row['total_weightage']) * 100, 2);
    }
    $data[] = [
        'course' => $row['course_name'],
        'clo_achievement' => $overall_percentage
    ];
}

echo json_encode($data);
?>
