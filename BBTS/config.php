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
        email TEXT,
        photo TEXT,
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

// Migrate database to add photo column if it doesn't exist
function migrateDatabase() {
    $db = getDBConnection();
    
    try {
        // Check if photo column exists
        $result = $db->query("PRAGMA table_info(applications)");
        $columns = $result->fetchAll(PDO::FETCH_ASSOC);
        $hasPhotoColumn = false;
        
        foreach ($columns as $column) {
            if ($column['name'] === 'photo') {
                $hasPhotoColumn = true;
                break;
            }
        }
        
        // Add photo column if it doesn't exist
        if (!$hasPhotoColumn) {
            $db->exec("ALTER TABLE applications ADD COLUMN photo TEXT");
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Database migration failed: " . $e->getMessage());
        return false;
    }
}

// Initialize database on first load
if (!file_exists(DB_PATH)) {
    initializeDatabase();
} else {
    // Run migration for existing databases
    migrateDatabase();
}
?>
