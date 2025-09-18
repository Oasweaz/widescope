<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'freight_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        // Try to create database if it doesn't exist
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Create database
        $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $conn->select_db(DB_NAME);
        
        // Create tables
        $conn->query("CREATE TABLE IF NOT EXISTS freight (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tracking_number VARCHAR(50) NOT NULL UNIQUE,
            consignee VARCHAR(100) NOT NULL,
            origin VARCHAR(100) NOT NULL,
            destination VARCHAR(100) NOT NULL,
            contents TEXT NOT NULL,
            weight DECIMAL(10,2) NOT NULL,
            status ENUM('Pending', 'In Transit', 'Out for Delivery', 'Delivered', 'Delayed') DEFAULT 'Pending',
            estimated_arrival DATE,
            carrier VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Add sample data
        $result = $conn->query("SELECT COUNT(*) as count FROM freight");
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            $conn->query("INSERT INTO freight (tracking_number, consignee, origin, destination, contents, weight, status, estimated_arrival, carrier) VALUES
                ('TRK123456', 'John Smith', 'New York', 'Los Angeles', 'Electronics', 150.75, 'In Transit', '2023-12-15', 'FedEx'),
                ('TRK789012', 'Sarah Johnson', 'Chicago', 'Miami', 'Clothing', 200.50, 'Out for Delivery', '2023-12-20', 'UPS'),
                ('TRK345678', 'Robert Williams', 'Seattle', 'Boston', 'Books', 75.25, 'Delivered', '2023-12-05', 'DHL'),
                ('TRK901234', 'Emily Davis', 'Houston', 'Atlanta', 'Furniture', 500.00, 'Delayed', '2023-12-22', 'USPS')");
        }
    }
    
    return $conn;
}
?>