<?php
// Include PhpSpreadsheet library
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["import"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $spreadsheet = IOFactory::load($fileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Process each row
        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Skip header row

            // Extract course data
            $course_code = trim($row[0] ?? '');
            $course_title = trim($row[1] ?? '');
            $credits = trim($row[2] ?? '');
            $category = trim($row[3] ?? '');
            $semester = trim($row[4] ?? '');
            $clo = trim($row[5] ?? ''); // This is the CLO for test_courses

            if (empty($course_code) || empty($course_title)) {
                continue; // Skip rows with missing course code or title
            }

            // Check if the course already exists
            $stmt = $conn->prepare("SELECT * FROM test_courses WHERE Course_Code = ?");
            $stmt->bind_param("s", $course_code);
            $stmt->execute();
            $courseResult = $stmt->get_result();
            $stmt->close();

            // Insert or update test_courses
            if ($courseResult->num_rows == 0) {
                // Insert new course
                $stmt = $conn->prepare("INSERT INTO test_courses (Course_Code, Course_Title, Credits, Category, Semester, CLO) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisss", $course_code, $course_title, $credits, $category, $semester, $clo);
                if (!$stmt->execute()) {
                    echo "Error inserting into test_courses: " . $stmt->error . "<br>";
                }
                $stmt->close();
            }

            // Extract CLO data (Using correct indexes)
            $clo_num = trim($row[6] ?? '');
            $clo_desc = trim($row[7] ?? '');
            $level = trim($row[8] ?? '');
            $clo_domain = trim($row[9] ?? '');
            $plo = trim($row[10] ?? '');

            // Insert into test_clo if CLO number is not empty
            if (!empty($clo_num)) {
                $stmt = $conn->prepare("INSERT INTO test_clo (Course_Code, CLONUM, CLODESC, LEVEL, CLODOMAIN, PLO) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $course_code, $clo_num, $clo_desc, $level, $clo_domain, $plo);
                if (!$stmt->execute()) {
                    echo "Error inserting into test_clo: " . $stmt->error . "<br>";
                }
                $stmt->close();
            }
        }

        header("Location: testobeicourses.php");
        exit(); 
    }
}

$conn->close();
?>
