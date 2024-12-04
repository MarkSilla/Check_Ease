<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'Cors.php';
include 'db.php'; 
include 'validate.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (empty($authHeader)) {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';
    }

    if (empty($authHeader)) {
        echo json_encode(['success' => false, 'error' => 'Authorization token missing or malformed']);
        exit;
    }

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        echo json_encode(['success' => false, 'error' => 'Authorization token missing']);
        exit;
    }

    $jwt = $matches[1]; 

    // Validate the JWT
    $userId = validateJWT($jwt);

    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        exit;
    }

    // Get and log the received data for debugging
    $postData = json_decode(file_get_contents('php://input'), true);
    error_log('Received data: ' . print_r($postData, true));

    if (empty($postData)) {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
        exit;
    }

    $className = trim($postData['class_name'] ?? '');
    $capacity = trim($postData['capacity'] ?? '');
    $section = trim($postData['section'] ?? '');

    error_log("Class Name: $className");
    error_log("Capacity: $capacity");
    error_log("Section: $section");

    error_log("Class: $className, Capacity: $capacity, Section: $section"); 

    if (empty($className) || !is_numeric($capacity) || $capacity <= 0 || empty($section)) {
        echo json_encode(['success' => false, 'error' => 'Invalid class name, capacity, or section']);
        exit;
    }

    $classCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

    try {
        $stmt = $pdo->prepare("INSERT INTO classes (class_name, capacity, created_by, class_code, section) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$className, $capacity, $userId, $classCode, $section]);

        if ($stmt->rowCount() > 0) {
            
            echo json_encode([
                'success' => true,
                'class' => [
                    'class_name' => $className,
                    'capacity' => $capacity,
                    'class_code' => $classCode,
                    'section' => $section
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to insert class into the database']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        error_log('Database error: ' . $e->getMessage()); // Log the error details
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn = null;
?>
