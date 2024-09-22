<?php
// Database configuration
define('DB_HOST', 'localhost');  // Usually 'localhost' for XAMPP
define('DB_NAME', 'smse');
define('DB_USER', 'root');  // Default XAMPP username
define('DB_PASS', '');  // Default XAMPP password (empty)

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>