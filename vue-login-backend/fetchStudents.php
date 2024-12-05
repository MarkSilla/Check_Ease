<?php

// Include necessary files
include 'Cors.php';  
require __DIR__ . '/vendor/autoload.php';  // Composer autoloader
require('db.php');  
require 'validate.php'; 

// Get headers
$headers = getallheaders();

// Check for Authorization header
if (!isset($headers['Authorization'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Missing Authorization Header']);
    exit;  
}

$authHeader = $headers['Authorization'];
list($jwt) = sscanf($authHeader, 'Bearer %s');  

// If JWT token is missing
if (!$jwt) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Missing JWT token']);
    exit;
}

try {
    // Decode JWT token
    $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($secret_key, 'HS256'));
    $user_id = $decoded->data->id;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'students' => $students]);

} catch (Exception $e) {
    // Log error if JWT is invalid or expired
    error_log("Error decoding JWT: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Invalid or expired JWT token']);
} catch (PDOException $e) {
    // Log error if there's a problem with the database query
    error_log("Error fetching students: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching students']);
}

?>
