<?php
header('Content-Type: application/json');
include 'db.php';

// Get the last notification ID from the request (for polling)
$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

// Fetch new notifications
$sql = "SELECT * FROM notifications WHERE id > ? ORDER BY created_at DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $last_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'message' => $row['message'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['notifications' => $notifications]);
$stmt->close();
$conn->close();
?> 