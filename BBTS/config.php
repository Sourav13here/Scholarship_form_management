<?php
// Database configuration
define('DB_PATH', __DIR__ . '/scholarship.db');

// Create database connection
function getDBConnection() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Initialize database tables
function initializeDatabase() {
    $db = getDBConnection();
    
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS applications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        application_id TEXT UNIQUE NOT NULL,
        name TEXT NOT NULL,
        class TEXT NOT NULL,
        school TEXT NOT NULL,
        address TEXT NOT NULL,
        contact TEXT NOT NULL,
        alt_contact TEXT,
        email TEXT NOT NULL,
        achievements TEXT,
        declaration INTEGER NOT NULL,
        submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status TEXT DEFAULT 'pending'
    )";
    
    try {
        $db->exec($createTableSQL);
        return true;
    } catch(PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

// Initialize database on first load
if (!file_exists(DB_PATH)) {
    initializeDatabase();
}
?>
