<?php
require __DIR__ . '/vendor/autoload.php'; 
require 'db.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = getenv('JWT_SECRET_KEY');

/**
 * Validates the JWT and returns the user ID if valid.
 *
 * @param string 
 * @return int|null
 */
function validateJWT($jwt) {
    global $pdo, $secret_key;

    try {
        // Decode the JWT
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));

        // Check if the token is expired
        if (isset($decoded->exp) && $decoded->exp < time()) {
            return null; // Return null if the token is expired
        }

        $userId = $decoded->data->id ?? null; // Get the user ID from the decoded data

        if (!$userId) {
            return null; // Return null if there's no user ID
        }

        // Check if the user exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? (object)['id' => $userId] : null; // Return the user ID if the user is found

    } catch (Exception $e) {
        error_log("JWT validation error: " . $e->getMessage());
        return null; // Return null if there's an error
    }
}
?>

