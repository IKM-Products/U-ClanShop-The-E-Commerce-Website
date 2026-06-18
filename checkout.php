<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS requests smoothly to bypass CORS restrictions
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database configuration link details
$servername = "127.0.0.1";
$username = "root";
$password = ""; // Matching your active blank development server credential 
$dbname = "uclan_shop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database link failure"]);
    exit();
}

// Parse incoming raw body stream text payload
$inputData = json_decode(file_get_contents("php://input"), true);

if (!empty($inputData['name']) && !empty($inputData['email']) && !empty($inputData['cart'])) {
    $customerName = mysqli_real_escape_string($conn, $inputData['name']);
    $email = mysqli_real_escape_string($conn, $inputData['email']);
    
    // Calculate total amount sum processing loop
    $totalAmount = 0;
    foreach ($inputData['cart'] as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    // Insert Parent Main Order Entry row
    $orderQuery = "INSERT INTO tbl_orders (customer_name, email, total_amount) VALUES ('$customerName', '$email', '$totalAmount')";
    
    if ($conn->query($orderQuery) === TRUE) {
        $orderId = $conn->insert_id;

        // Insert individual child relational order items breakdown list
        foreach ($inputData['cart'] as $item) {
            $pId = (int)$item['id'];
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];
            
            $itemQuery = "INSERT INTO tbl_order_items (order_id, product_id, quantity, price) VALUES ($orderId, $pId, $qty, $price)";
            $conn->query($itemQuery);
        }

        echo json_encode(["success" => true, "order_id" => $orderId]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to save order transaction details."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Incomplete checkout data stream parameters submitted."]);
}

$conn->close();
?>