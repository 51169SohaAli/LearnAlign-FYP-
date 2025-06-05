<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "obe";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$data = [
    'clo' => [],
    'plo' => [],
    'peo' => []
];

$query_clo = "SELECT clo, description, plo, course_id FROM clo";
$result_clo = $conn->query($query_clo);
while ($row = $result_clo->fetch_assoc()) {
    $data['clo'][] = $row;
}

$query_plo = "SELECT plo, description, peo, program_id FROM plo";
$result_plo = $conn->query($query_plo);
while ($row = $result_plo->fetch_assoc()) {
    $data['plo'][] = $row;
}

$query_peo = "SELECT peo, description, program_id FROM peo";
$result_peo = $conn->query($query_peo);
while ($row = $result_peo->fetch_assoc()) {
    $data['peo'][] = $row;
}

$conn->close();
echo json_encode($data);
?>