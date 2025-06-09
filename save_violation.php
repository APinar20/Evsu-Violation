<?php
// save_violation.php

$host = 'localhost';
$port = 3307;
$user = 'root';
$password = '';
$database = 'systemviolation';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight CORS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$conn = new mysqli($host, $user, $password, $database, $port);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input.']);
    exit;
}

$student_id  = $data['student_id'] ?? '';
$first_name  = $data['fname'] ?? '';
$last_name   = $data['lname'] ?? '';
$mi          = $data['mname'] ?? '';
$course      = $data['program'] ?? '';
$section     = $data['department'] ?? '';
$violation   = $data['violation'] ?? '';
$todo        = $data['todo'] ?? '';

if (empty($student_id) || empty($violation)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing (student_id or violation).']);
    exit;
}

$sql = "INSERT INTO student_violations 
        (student_id, first_name, last_name, mi, course, section, violation, todo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param('ssssssss', 
    $student_id, 
    $first_name, 
    $last_name, 
    $mi, 
    $course, 
    $section, 
    $violation, 
    $todo
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Violation saved successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
