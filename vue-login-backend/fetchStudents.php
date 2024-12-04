<?php
include 'Cors.php';
require __DIR__ . '/vendor/autoload.php';
require '../db.php';
session_start();

// Check if the session user_id is set and valid
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Missing session user_id']);
    exit;
}

try {
    // Prepare and execute the query to fetch students
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'student'");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the result as a JSON response
    echo json_encode(['success' => true, 'students' => $students]);
} catch (PDOException $e) {
    // Log the error for debugging purposes
    error_log("Error fetching students: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching students']);
}
?>
