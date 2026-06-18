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

// 2. Extract and Validate Raw POST Data
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Please provide both username and password"]);
    exit;
}

try {
    // 3. Establish JSON File Path (Shared Ecosystem)
    $database_file = __DIR__ . '/uclan_shop_users.json';

    // Verify the database file exists
    if (!file_exists($database_file)) {
        echo json_encode(["success" => false, "error" => "User not found"]);
        exit;
    }

    $users = json_decode(file_get_contents($database_file), true);
    if (!is_array($users)) { $users = []; }

    // 4. Authenticate User Credentials (Simulating SQL match query)
    $matched_user = null;

    foreach ($users as $user) {
        // Look up by matching username (supports email login fallback too for better UX)
        if ((isset($user['username']) && strtolower($user['username']) === strtolower($username)) || 
            (isset($user['user_email']) && strtolower($user['user_email']) === strtolower($username))) {
            $matched_user = $user;
            break;
        }
    }

    // 5. Evaluate Matches and Verify Hashed Password
    if ($matched_user) {
        if (password_verify($password, $matched_user['password'])) {
            echo json_encode([
                "success" => true, 
                "user" => [
                    "id" => $matched_user['user_id'], 
                    "name" => $matched_user['username']
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "User not found"]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Login system operational failure.",
        "debug_message" => $e->getMessage()
    ]);
}
?>