<?php
// resolve_violation.php

$host = 'localhost';
$port = 3307;
$user = 'root';
$password = '';
$database = 'systemviolation';

header('Content-Type: application/json');

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE student_violations SET resolved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update record']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
}

$conn->close();
