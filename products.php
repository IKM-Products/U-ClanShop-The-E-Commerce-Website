<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allows secure cross-origin file communication

$servername = "localhost";
$username = "root";       // Standard local development user
$password = "";           // Standard local development blank password
$dbname = "uclan_shop";   // Name of your imported database schema container

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch products from the imported SQL table schema directly
$sql = "SELECT * FROM tbl_products";
$result = $conn->query($sql);

$products = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>