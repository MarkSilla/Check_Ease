<?php
require __DIR__ . '/vendor/autoload.php'; 
require 'db.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = getenv('JWT_SECRET_KEY'); 

/**
 * Validates the JWT and returns the user ID if valid.
 *
 * @param string $jwt
 * @return int|null
 */
function validateJWT($jwt) {
    global $pdo, $secret_key; 

    try {
        // Decode the JWT
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        
        // Check if the token is expired
        if (isset($decoded->exp) && $decoded->exp < time()) {
            header('Location: /login.php'); 
            exit(); 
        }

        $userId = $decoded->data->id ?? null; 

        if (!$userId) {
            return null; 
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $userId : null; 

    } catch (Exception $e) {
        error_log("JWT validation error: " . $e->getMessage());
        return null; 
    }
}
?>

