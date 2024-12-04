<?php
include 'Cors.php';  
require __DIR__ . '/vendor/autoload.php';  
require '../db.php';  
session_start();  

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the input data
    if (!isset($data['class_id']) || !isset($data['attendance']) || !is_array($data['attendance'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $class_id = (int)$data['class_id'];
    $attendance_data = $data['attendance'];

    // Validate class_id
    if ($class_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid class ID']);
        exit;
    }

    // Iterate through the attendance data and insert/update each record
    try {
        foreach ($attendance_data as $student_id => $status) {
            // Validate student_id and status
            if (!is_int($student_id) || !in_array($status, ['present', 'absent', 'late'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid student ID or status']);
                exit;
            }

            // Check if the attendance record already exists for the student in the class
            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE class_id = ? AND student_id = ?");
            $stmt->execute([$class_id, $student_id]);

            if ($stmt->rowCount() > 0) {
                // Update the existing attendance record
                $stmt = $pdo->prepare("UPDATE attendance SET status = ?, updated_at = NOW() WHERE class_id = ? AND student_id = ?");
                $stmt->execute([$status, $class_id, $student_id]);
            } else {
                // Insert a new attendance record
                $stmt = $pdo->prepare("INSERT INTO attendance (class_id, student_id, attendance_date, status) VALUES (?, ?, NOW(), ?)");
                $stmt->execute([$class_id, $student_id, $status]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Attendance updated successfully']);
    } catch (PDOException $e) {
        // Catch database errors
        echo json_encode(['success' => false, 'message' => 'Error updating attendance', 'error' => $e->getMessage()]);
    }
}
?>
