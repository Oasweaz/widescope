<?php
require_once 'config.php';

// Get freight item by tracking number
function getItemByTrackingNumber($tracking_number) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM freight WHERE tracking_number = ?");
    $stmt->bind_param("s", $tracking_number);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get all inventory items
function getInventory() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM freight ORDER BY created_at DESC");
    $inventory = [];
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
    return $inventory;
}

// Search inventory
function searchInventory($search_term) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM freight WHERE tracking_number LIKE ? OR consignee LIKE ? OR origin LIKE ? OR destination LIKE ? ORDER BY created_at DESC");
    $search_pattern = "%$search_term%";
    $stmt->bind_param("ssss", $search_pattern, $search_pattern, $search_pattern, $search_pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $inventory = [];
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
    return $inventory;
}

// Add new item
function addItem($data) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO freight (tracking_number, consignee, origin, destination, contents, weight, status, estimated_arrival, carrier) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdsss", $data['tracking_number'], $data['consignee'], $data['origin'], $data['destination'], $data['contents'], $data['weight'], $data['status'], $data['estimated_arrival'], $data['carrier']);
    return $stmt->execute();
}

// Update item
function updateItem($id, $data) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE freight SET tracking_number=?, consignee=?, origin=?, destination=?, contents=?, weight=?, status=?, estimated_arrival=?, carrier=? WHERE id=?");
    $stmt->bind_param("ssssdssssi", $data['tracking_number'], $data['consignee'], $data['origin'], $data['destination'], $data['contents'], $data['weight'], $data['status'], $data['estimated_arrival'], $data['carrier'], $id);
    return $stmt->execute();
}

// Delete item
function deleteItem($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM freight WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Get item by ID
function getItem($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM freight WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>