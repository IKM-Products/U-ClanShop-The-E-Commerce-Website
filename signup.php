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
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "All fields are required."]);
    exit;
}

$username = trim($data['username']);
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$password = password_hash($data['password'], PASSWORD_DEFAULT); // Secure hashing intact!

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid email format."]);
    exit;
}

try {
    // 3. Establish JSON File Path (Shared with your forgot-password file!)
    $database_file = __DIR__ . '/uclan_shop_users.json';

    // Load existing users or initialize an empty array if the file is fresh
    $users = file_exists($database_file) ? json_decode(file_get_contents($database_file), true) : [];
    if (!is_array($users)) { $users = []; }

    // 4. Duplicate Check Logic
    $username_exists = false;
    $email_exists = false;

    foreach ($users as $user) {
        if (isset($user['username']) && strtolower($user['username']) === strtolower($username)) {
            $username_exists = true;
            break;
        }
        if (isset($user['user_email']) && strtolower($user['user_email']) === strtolower($email)) {
            $email_exists = true;
            break;
        }
    }

    if ($username_exists) {
        http_response_code(200); // Handled graceful validation
        echo json_encode(["success" => false, "error" => "Username already taken"]);
        exit;
    }

    if ($email_exists) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Email address already registered"]);
        exit;
    }

    // 5. Insert New User Payload
    $new_user_id = count($users) > 0 ? max(array_column($users, 'user_id')) + 1 : 1;
    
    $users[] = [
        "user_id" => $new_user_id,
        "username" => $username,
        "user_email" => $email,
        "password" => $password,
        "user_address" => "Not provided"
    ];

    // Write array modifications back onto local disk tracking matrix
    file_put_contents($database_file, json_encode($users, JSON_PRETTY_PRINT));

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Registration workflow context processing exception.",
        "debug_message" => $e->getMessage()
    ]);
}
?>