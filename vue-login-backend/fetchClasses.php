<?php
require __DIR__ . '/vendor/autoload.php';
include 'Cors.php';
require 'db.php';
require 'validate.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the Authorization header
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (empty($authHeader)) {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';
    }

    if (empty($authHeader)) {
        echo json_encode(['success' => false, 'error' => 'Authorization token missing or malformed']);
        exit;
    }

    // Extract token using regex
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        echo json_encode(['success' => false, 'error' => 'Authorization token missing']);
        exit;
    }

    $jwt = $matches[1];

    // Validate the JWT token and get the user ID
    $userId = validateJWT($jwt); // Validate the JWT and return the user ID

    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        exit;
    }

    try {
        // Query to get all classes that were created by the logged-in user
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE created_by = ?");
        $stmt->execute([$userId]);

        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        

            if ($classes) {
                echo json_encode(['success' => true, 'classes' => $classes]);
            } else {
                echo json_encode(['success' => true, 'classes' => []]); 
            }

    } catch (PDOException $e) {
        // Detailed error message in case of DB issues
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn = null;  // Close the connection
?>
