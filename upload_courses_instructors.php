<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if (isset($_FILES['assignment_file']) && isset($_POST['semester_id'])) {
    $file = $_FILES['assignment_file'];
    $semester_id = $_POST['semester_id'];

    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($file['name']);

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'xlsx') {
        echo json_encode(['success' => false, 'message' => 'Only XLSX files are allowed.']);
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        try {
            $spreadsheet = IOFactory::load($uploadFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip the first row if it's a header
            array_shift($rows);

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "obe";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                echo json_encode(['success' => false, 'message' => 'Database connection failed']);
                exit();
            }

            $stmt = $conn->prepare("INSERT INTO course_instructor (course_id, instructor_id, semester_id) VALUES (?, ?, ?)");

            foreach ($rows as $row) {
                $course_id = $row[0];
                $instructor_id = $row[1];

                $stmt->bind_param("ssi", $course_id, $instructor_id, $semester_id);
// ^     ^     ^
// s     s     i → for strings, strings, and integer

                $stmt->execute();
            }

            $stmt->close();
            $conn->close();

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error reading Excel file: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading the file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing file or semester ID.']);
}
?>
