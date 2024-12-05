<?php
// Include the necessary files
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'Cors.php';
include 'db.php';
include 'validate.php';

session_start();

$authorizationHeader = getAuthorizationHeader();
error_log("Authorization Header: " . $authorizationHeader);

if (!$authorizationHeader) {
    echo json_encode(['success' => false, 'message' => 'Missing Token']);
    exit;
}

list($jwt) = sscanf($authorizationHeader, 'Bearer %s');
error_log("JWT Token: " . $jwt);

$decoded = validateJWT($jwt);

if (!$decoded) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Invalid or Expired JWT']);
    exit;
}

$user_id = $decoded->id;
error_log("User ID: " . $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $first_name = $data['first_name'] ?? null;
    $last_name = $data['last_name'] ?? null;

    // Validate that first name and last name are provided
    if (!$first_name || !$last_name) {
        echo json_encode(['success' => false, 'message' => 'First name and last name are required.']);
        exit;
    }

    try {
        // Insert the student into the students table
        $sql = "INSERT INTO students (first_name, last_name, user_id) VALUES (:first_name, :last_name, :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':user_id' => $user_id,
        ]);

        $student_id = $pdo->lastInsertId();


        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to add student: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
