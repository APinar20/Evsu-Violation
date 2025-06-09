<?php
// update_violation.php

header('Content-Type: application/json');

$host = 'localhost';
$port = 3307;
$user = 'root';
$password = '';
$database = 'systemviolation';

// Connect to the database
$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

// Collect POST data
$id = $_POST['id'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$mi = $_POST['mi'] ?? '';
$violation = $_POST['violation'] ?? '';
$todo = $_POST['todo'] ?? '';

// Validate required fields
if (!$id || !$violation || !$todo || !$first_name || !$last_name) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
    exit;
}

// Prepare update statement including name fields
$stmt = $conn->prepare("
    UPDATE student_violations 
    SET first_name = ?, last_name = ?, mi = ?, violation = ?, todo = ? 
    WHERE id = ?
");
$stmt->bind_param('sssssi', $first_name, $last_name, $mi, $violation, $todo, $id);

// Execute and respond
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>
