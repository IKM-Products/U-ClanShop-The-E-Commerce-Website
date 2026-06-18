<?php
// router.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// If the requested file exists, serve it as-is
if (file_exists($_SERVER["SCRIPT_FILENAME"])) {
    return false; 
}

// Fallback routing configuration
http_response_code(404);
echo json_encode(["success" => false, "error" => "Resource not found inside engine runtime environment."]);
?>