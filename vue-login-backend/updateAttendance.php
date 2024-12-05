<?php
include 'Cors.php';  
require __DIR__ . '/vendor/autoload.php';  
require '../db.php';  
session_start();  

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['class_id']) || !isset($data['attendance']) || !is_array($data['attendance'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $class_id = (int)$data['class_id'];
    $attendance_data = $data['attendance'];

    if ($class_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid class ID']);
        exit;
    }

    try {
        foreach ($attendance_data as $student_id => $status) {
            if (!is_int($student_id) || !in_array($status, ['present', 'absent', 'late'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid student ID or status']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE class_id = ? AND student_id = ?");
            $stmt->execute([$class_id, $student_id]);

            if ($stmt->rowCount() > 0) {
                $stmt = $pdo->prepare("UPDATE attendance SET status = ?, updated_at = NOW() WHERE class_id = ? AND student_id = ?");
                $stmt->execute([$status, $class_id, $student_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO attendance (class_id, student_id, attendance_date, status) VALUES (?, ?, NOW(), ?)");
                $stmt->execute([$class_id, $student_id, $status]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Attendance updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating attendance', 'error' => $e->getMessage()]);
    }
}
?>
