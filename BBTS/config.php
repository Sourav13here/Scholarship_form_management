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
        // Inspect current schema
        $result = $db->query("PRAGMA table_info(applications)");
        $columns = $result->fetchAll(PDO::FETCH_ASSOC);
        $hasPhotoColumn = false;
        $emailNotNull = false;

        foreach ($columns as $column) {
            if ($column['name'] === 'photo') {
                $hasPhotoColumn = true;
            }
            if ($column['name'] === 'email' && (int)$column['notnull'] === 1) {
                $emailNotNull = true;
            }
        }

        // Add photo column if it doesn't exist
        if (!$hasPhotoColumn) {
            $db->exec("ALTER TABLE applications ADD COLUMN photo TEXT");
        }

        // If email is NOT NULL, recreate table with email nullable
        if ($emailNotNull) {
            $db->beginTransaction();
            try {
                // Create new table with desired schema
                $db->exec("CREATE TABLE IF NOT EXISTS applications_new (
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
                )");

                // Copy data
                $db->exec("INSERT INTO applications_new (
                    id, application_id, name, class, school, address, contact, alt_contact, email, photo, achievements, declaration, submission_date, status
                ) SELECT id, application_id, name, class, school, address, contact, alt_contact, email, photo, achievements, declaration, submission_date, status FROM applications");

                // Replace old table
                $db->exec("DROP TABLE applications");
                $db->exec("ALTER TABLE applications_new RENAME TO applications");

                $db->commit();
            } catch (PDOException $e) {
                $db->rollBack();
                throw $e;
            }
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
