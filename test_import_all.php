<?php
// Include PhpSpreadsheet Library
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

// Function to safely get cell value
function getCellValue($cellIterator) {
    $value = trim($cellIterator->current()->getFormattedValue());
    $cellIterator->next();
    return ($value === "" || $value === null) ? null : $value;
}

// Enable detailed MySQL error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST['import'])) {
    if ($_FILES['file']['name']) {
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fileSize = $_FILES['file']['size'];
        $allowedTypes = ['xlsx', 'xls'];
        
        if (!in_array($fileType, $allowedTypes)) {
            die("Only Excel files (.xlsx, .xls) are allowed.");
        }
        
        if ($fileSize > 5 * 1024 * 1024) {
            die("File is too large. Maximum size: 5MB.");
        }

        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        if (file_exists($target_file)) {
            unlink($target_file);
        }

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            die("Error uploading file.");
        }

        echo "✅ File uploaded successfully.<br>";
        $spreadsheet = IOFactory::load($target_file);
        $sheet = $spreadsheet->getActiveSheet();

        // Start transaction to ensure atomicity
        $conn->begin_transaction();

        try {
            foreach ($sheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cellIterator->rewind();

                // Fetching data
                $student_id = getCellValue($cellIterator);
                $name = getCellValue($cellIterator);
                $intake_batch = getCellValue($cellIterator);
                $institute_id = getCellValue($cellIterator);
                $institute_name = getCellValue($cellIterator);
                $program_id = getCellValue($cellIterator);
                $program_name = getCellValue($cellIterator);
                $program_type = getCellValue($cellIterator);
                $registered_module_id = getCellValue($cellIterator);
                $registered_course_name = getCellValue($cellIterator);
                $registered_course_name_short = getCellValue($cellIterator);
                $module_group_id = getCellValue($cellIterator);
                $module_group = getCellValue($cellIterator);
                $module_category_id = getCellValue($cellIterator);
                $module_category_name = getCellValue($cellIterator);
                $credit_hours = floatval(getCellValue($cellIterator));
                $booking_date_raw = getCellValue($cellIterator);
$booking_date = ($booking_date_raw && strtotime($booking_date_raw)) ? date('Y-m-d', strtotime($booking_date_raw)) : null;
                $event_package_id_raw = getCellValue($cellIterator);
$event_package_id = is_numeric($event_package_id_raw) ? intval($event_package_id_raw) : null;
                $event_package_abbr = getCellValue($cellIterator);
                $event_package = getCellValue($cellIterator);
                $year = getCellValue($cellIterator);
                $session = getCellValue($cellIterator);

                // Ensure required fields exist
               $rowNumber = $row->getRowIndex(); // Get the current row number
if (!$student_id || !$name || !$institute_id || !$program_id) {
    echo "⚠️ Skipping row $rowNumber due to missing required values.<br>";
    continue;
}

                // Validate booking date format
                if ($booking_date && !strtotime($booking_date)) {
                    echo "❌ Invalid date format for booking_date: $booking_date. Skipping row.<br>";
                    continue;
                }

                // Insert Institute
                $stmt = $conn->prepare("INSERT INTO institute (institute_id, institute_name)
                                        VALUES (?, ?) ON DUPLICATE KEY UPDATE institute_name = VALUES(institute_name)");
                $stmt->bind_param("is", $institute_id, $institute_name);
                $stmt->execute();
                $stmt->close();

                // Insert Program
                $stmt = $conn->prepare("INSERT INTO program (program_id, program_name, program_type)
                                        VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE program_name = VALUES(program_name)");
                $stmt->bind_param("iss", $program_id, $program_name, $program_type);
                $stmt->execute();
                $stmt->close();

                // Insert Module Group
                if ($module_group_id) {
                    $stmt = $conn->prepare("INSERT INTO modulegroups (ModuleGroupID, ModuleGroup)
                                            VALUES (?, ?) ON DUPLICATE KEY UPDATE ModuleGroup = VALUES(ModuleGroup)");
                    $stmt->bind_param("is", $module_group_id, $module_group);
                    $stmt->execute();
                    $stmt->close();
                }

                // Insert Module Category
                if ($module_category_id) {
                    $stmt = $conn->prepare("INSERT INTO modulecategories (ModuleCategoryID, ModuleCategoryName)
                                            VALUES (?, ?) ON DUPLICATE KEY UPDATE ModuleCategoryName = VALUES(ModuleCategoryName)");
                    $stmt->bind_param("is", $module_category_id, $module_category_name);
                    $stmt->execute();
                    $stmt->close();
                }

                // Insert Modules
                if ($registered_module_id) {
                    $stmt = $conn->prepare("INSERT INTO modules (RegisteredModuleID, RegisteredCourseName, RegisteredCourseNameShort, ModuleGroupID, ModuleCategoryID, CreditHours)
                                            VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE RegisteredCourseName = VALUES(RegisteredCourseName)");
                    $stmt->bind_param("issiis", $registered_module_id, $registered_course_name, $registered_course_name_short, $module_group_id, $module_category_id, $credit_hours);
                    $stmt->execute();
                    $stmt->close();
                }

                // Insert Event Packages
                if ($event_package_id) {
                    $stmt = $conn->prepare("INSERT INTO eventpackages (EventPackageID, EventPackageAbbreviation, EventPackage, Year, Session)
                                            VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE EventPackageAbbreviation = VALUES(EventPackageAbbreviation)");
                    $stmt->bind_param("issis", $event_package_id, $event_package_abbr, $event_package, $year, $session);
                    $stmt->execute();
                    $stmt->close();
                }

                // Insert Student
                $stmt = $conn->prepare("INSERT INTO loginstudent (student_id, name, intake_batch, institute_id, program_id)
                                        VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
                $stmt->bind_param("issii", $student_id, $name, $intake_batch, $institute_id, $program_id);
                $stmt->execute();
                $stmt->close();

               // Insert Student Module Registration
if ($registered_module_id) {
    $stmt = $conn->prepare("INSERT INTO studentmoduleregistrations (student_id, RegisteredModuleID, BookingDate, EventPackageID)
                            VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE BookingDate = VALUES(BookingDate), EventPackageID = VALUES(EventPackageID)");
    $stmt->bind_param("iisi", $student_id, $registered_module_id, $booking_date, $event_package_id);
    $stmt->execute();
    $stmt->close();
}
            }

            $conn->commit();
            echo "✅ Data imported successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
            echo "❌ Import failed: " . $e->getMessage();
        }

        $conn->close();
    }
}
?>
