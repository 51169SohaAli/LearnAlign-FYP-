<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if (isset($_FILES['enrollment_file']) && isset($_POST['semester_id'])) {
    $file = $_FILES['enrollment_file'];
    $semester_id = $_POST['semester_id'];

    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($file['name']);

      $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "obe";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                echo json_encode(['success' => false, 'message' => 'Database connection failed']);
                exit();
            }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'xlsx') {
        echo json_encode(['success' => false, 'message' => 'Only XLSX files are allowed.']);
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        $spreadsheet = IOFactory::load($uploadFile);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header row
        unset($rows[0]);

        $conn = new mysqli("localhost", "root", "", "obe");
        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO students_course (student_id, course_id, semester_id) VALUES (?, ?, ?)");

        foreach ($rows as $row) {
            $student_id = $row[0];
            $course_id = $row[1];

            $stmt->bind_param("isi", $student_id, $course_id, $semester_id);
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload the file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing file or semester ID.']);
}
?>
