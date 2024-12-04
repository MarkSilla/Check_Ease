<?php
include 'Cors.php';  
require __DIR__ . '/vendor/autoload.php';  
require '../db.php';  
session_start();  

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $class_id = (int)$data['class_id'];
    $student_id = (int)$data['student_id'];

    try {
        $stmt = $pdo->prepare("SELECT id FROM class_students WHERE class_id = ? AND student_id = ?");
        $stmt->execute([$class_id, $student_id]);

        if ($stmt->rowCount() === 0) {
            $stmt = $pdo->prepare("INSERT INTO class_students (class_id, student_id) VALUES (?, ?)");
            $stmt->execute([$class_id, $student_id]);
            echo json_encode(['success' => true, 'message' => 'Student added to class']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Student already in class']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error adding student']);
    }
}
?>
