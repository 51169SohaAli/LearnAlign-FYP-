<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include Composer autoloader
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

// Handle file upload and process data
if (isset($_POST["import"]) && isset($_FILES["file"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if (pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION) == "xlsx") {
        try {
            // Load the Excel file
            $reader = IOFactory::createReaderForFile($fileName);
            $spreadsheet = $reader->load($fileName);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Prepare SQL statement for inserting data
            $stmt = $conn->prepare("INSERT INTO courses (course_id, course_name, credits, instructor_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $course_id, $course_name, $credits, $instructor_id);

            $isFirstRow = true;
            $insertedCount = 0;
            foreach ($data as $row) {
                if ($isFirstRow) { // Skip the header row
                    $isFirstRow = false;
                    continue;
                }

                // Assign values from the row
                $course_id = $row[0];
                $course_name = $row[1];
                $credits = (int)$row[2];
                $instructor_id = $row[3];

                // Insert data
                if ($stmt->execute()) {
                    $insertedCount++;
                } else {
                    // Log errors for debugging
                    error_log("Failed to insert row: " . $course_id . " - Error: " . $stmt->error);
                }
            }

            $stmt->close();
        } catch (Exception $e) {
            error_log("Error reading file: " . $e->getMessage());
        }
    } else {
        error_log("Invalid file type. Only .xlsx files are allowed.");
    }

    // Redirect to testimportdata.php
    header("Location: testimportdata.php");
    exit; // Ensure no further script execution
}

$conn->close();
?>
