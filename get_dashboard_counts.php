<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

$response = [
    'courses' => 0,
    'instructors' => 0,
    'students' => 0,
    'latest_semester' => 'N/A'
];

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get latest semester ID and name
    $semesterQuery = $conn->query("SELECT semester_id, semester_name FROM semester ORDER BY semester_id DESC LIMIT 1");
    if ($row = $semesterQuery->fetch_assoc()) {
        $semester_id = $row['semester_id'];
        $response['latest_semester'] = $row['semester_name'];

        // Count distinct courses in this semester from course_instructor
        $courseQuery = $conn->query("SELECT COUNT(DISTINCT course_id) AS total FROM course_instructor WHERE semester_id = $semester_id");
        if ($row = $courseQuery->fetch_assoc()) {
            $response['courses'] = $row['total'];
        }

        // Count distinct instructors in this semester from course_instructor
        $instructorQuery = $conn->query("SELECT COUNT(DISTINCT instructor_id) AS total FROM course_instructor WHERE semester_id = $semester_id");
        if ($row = $instructorQuery->fetch_assoc()) {
            $response['instructors'] = $row['total'];
        }

        // Count distinct students enrolled in any course for this semester
        $studentQuery = $conn->query("SELECT COUNT(DISTINCT student_id) AS total FROM students_course WHERE semester_id = $semester_id");
        if ($row = $studentQuery->fetch_assoc()) {
            $response['students'] = $row['total'];
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
