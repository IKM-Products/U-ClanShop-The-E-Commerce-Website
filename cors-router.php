<?php
// cors-router.php

// 1. Intercept all entry traffic and force global CORS compliance
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// 2. Instantly short-circuit browser preflight checks
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 3. Fall through to serve the requested file normally
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if (file_exists($file) && !is_dir($file)) {
    return false; // Tells PHP server to serve the requested file as-is
}

// If the file doesn't exist, handle it cleanly
http_response_code(404);
echo json_encode(["success" => false, "error" => "File not found inside server context."]);
?>