<?php
// 1. Core Header Configuration & CORS Management
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:3001");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle the browser's "preflight" OPTIONS request instantly
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method Not Allowed."]);
    exit;
}

// 2. Extract Data Payload
$json_data = file_get_contents("php://input");
$request_data = json_decode($json_data, true);

if (empty($request_data['email'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Email address is required."]);
    exit;
}

$email = filter_var(trim($request_data['email']), FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid email format."]);
    exit;
}

try {
    // 3. Establish JSON File Storage Instance (No drivers or servers required!)
    $database_file = __DIR__ . '/uclan_shop_users.json';
    $resets_file = __DIR__ . '/password_resets.json';

    // If the mock users file doesn't exist, create an empty one with a test user
    if (!file_exists($database_file)) {
        $dummy_users = [
            [
                "user_id" => 1,
                "username" => "testuser",
                "user_email" => "test@example.com",
                "password" => "hashed_password_here" // Replace this with actual hashes later
            ]
        ];
        file_put_contents($database_file, json_encode($dummy_users, JSON_PRETTY_PRINT));
    }

    // Read existing users
    $users = json_decode(file_get_contents($database_file), true);
    
    // Check if the email exists in our records
    $user_found = false;
    foreach ($users as $user) {
        if (isset($user['user_email']) && strtolower($user['user_email']) === strtolower($email)) {
            $user_found = true;
            break;
        }
    }

    if ($user_found) {
        // Securely generate a temporary token string
        $token = bin2hex(random_bytes(32)); 
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour")); 
        
        // Load or create password resets tracker file
        $resets = file_exists($resets_file) ? json_decode(file_get_contents($resets_file), true) : [];
        
        // Wipe old reset records matching this email address to keep the database small
        $resets = array_filter($resets, function($item) use ($email) {
            return $item['email'] !== $email;
        });

        // Append new token tracking payload
        $resets[] = [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expires
        ];

        // Save back to local text file
        file_put_contents($resets_file, json_encode(array_values($resets), JSON_PRETTY_PRINT));
    }

    // Always report success to client to protect register data leak visibility metrics
    echo json_encode([
        "success" => true,
        "message" => "If the email is registered, a password reset link has been processed."
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "File tracking system operational failure caught inside workflow context.",
        "debug_message" => $e->getMessage()
    ]);
}
?>