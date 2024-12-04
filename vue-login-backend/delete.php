<?php
require __DIR__ . '/vendor/autoload.php';
include 'Cors.php';
require 'db.php';

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Invalid request method');
    }
    if (isset($_GET['class_code'])) {
        $class_code = $_GET['class_code'];
        if (empty($class_code)) {
            throw new Exception('Invalid input. Class code is required.');
        }
        
        $stmt = $pdo->prepare('DELETE FROM classes WHERE class_code = :class_code');
        $stmt->bindParam(':class_code', $class_code, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Class deleted successfully.'];
        } else {
            throw new Exception('Failed to delete class.');
        }
    } else {
        throw new Exception('Class code is missing.');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

echo json_encode($response);
?>
