<?php
include 'Cors.php';
require __DIR__ . '/vendor/autoload.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the JSON payload
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract values
    $email = $data['email'] ?? null;
    $newPassword = $data['newPassword'] ?? null;
    $reenterPassword = $data['reenterPassword'] ?? null;

    // Validate the inputs
    if (!$email || !$newPassword || !$reenterPassword) {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "All fields are required."]);
        exit;
    }

    if ($newPassword !== $reenterPassword) {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Passwords do not match."]);
        exit;
    }

    try {
        // Check if the email exists in the database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
       
            http_response_code(200); 
            echo json_encode(["message" => "Email not found."]);
            exit;
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password in the database
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $updateStmt->execute(['password' => $hashedPassword, 'email' => $email]);

        // Success response
        http_response_code(200); // OK
        echo json_encode(["message" => "Password reset successfully."]);

    } catch (Exception $e) {
        // Catch any errors and return them
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "An error occurred.", "error" => $e->getMessage()]);
    }
} else {
    // If the method is not POST, return method not allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Method not allowed."]);
}
?>
