<?php

require __DIR__ . '/vendor/autoload.php';
include 'Cors.php';
require 'db.php';

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $inputData = json_decode(file_get_contents('php://input'), true);

    if (empty($inputData['id']) || empty($inputData['class_name']) || empty($inputData['capacity'])) {
        throw new Exception('Invalid input. ID, class name, and capacity are required.');
    }

    $id = intval($inputData['id']);
    $className = trim($inputData['class_name']);
    $capacity = intval($inputData['capacity']);

    if ($capacity <= 0) {
        throw new Exception('Capacity must be a positive number.');
    }

    $stmt = $conn->prepare('UPDATE classes SET class_name = ?, capacity = ? WHERE id = ?');
    $stmt->bind_param('sii', $className, $capacity, $id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Class updated successfully.'];
    } else {
        throw new Exception('Failed to update class.');
    }

    $stmt->close();
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

echo json_encode($response);
?>
