<?php
require_once 'config.php';

$city = $_GET['city'] ?? '';
$profession = $_GET['profession'] ?? '';
$price_range = $_GET['price_range'] ?? '';

// Build dynamic query
$sql = "SELECT id, name, profession, city, charge, phone FROM users WHERE user_type = 'Worker'";

// Add conditions if filters are applied
$conditions = [];
if (!empty($city)) {
    $conditions[] = "city LIKE '%" . $conn->real_escape_string($city) . "%'";
}
if (!empty($profession)) {
    $conditions[] = "profession LIKE '%" . $conn->real_escape_string($profession) . "%'";
}
if (!empty($price_range) && is_numeric($price_range)) {
    $conditions[] = "charge <= " . intval($price_range);
}

// Append conditions to query
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$result = $conn->query($sql);

$workers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $workers[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($workers);
