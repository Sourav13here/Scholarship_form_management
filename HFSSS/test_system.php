<?php
/**
 * System Test Script
 * Run this to verify your installation
 */

echo "=================================================\n";
echo "  NUCLEON SCHOLARSHIP SYSTEM - TEST SCRIPT\n";
echo "=================================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// Test 1: PHP Version
echo "Testing PHP Version... ";
if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
    echo "✓ PASS (PHP " . PHP_VERSION . ")\n";
    $success[] = "PHP version is compatible";
} else {
    echo "✗ FAIL (PHP " . PHP_VERSION . ")\n";
    $errors[] = "PHP version must be 7.0 or higher";
}

// Test 2: PDO SQLite Extension
echo "Testing PDO SQLite Extension... ";
if (extension_loaded('pdo_sqlite')) {
    echo "✓ PASS\n";
    $success[] = "PDO SQLite extension is loaded";
} else {
    echo "✗ FAIL\n";
    $errors[] = "PDO SQLite extension is not loaded";
}

// Test 3: Write Permissions
echo "Testing Write Permissions... ";
$testFile = __DIR__ . '/test_write.tmp';
if (file_put_contents($testFile, 'test') !== false) {
    unlink($testFile);
    echo "✓ PASS\n";
    $success[] = "Directory has write permissions";
} else {
    echo "✗ FAIL\n";
    $errors[] = "Directory does not have write permissions";
}

// Test 4: Config File
echo "Testing Config File... ";
if (file_exists(__DIR__ . '/config.php')) {
    echo "✓ PASS\n";
    $success[] = "config.php exists";
} else {
    echo "✗ FAIL\n";
    $errors[] = "config.php not found";
}

// Test 5: Database Creation
echo "Testing Database Creation... ";
try {
    require_once __DIR__ . '/config.php';
    $db = getDBConnection();
    echo "✓ PASS\n";
    $success[] = "Database connection successful";
    
    // Check if table exists
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='applications'");
    if ($result->fetch()) {
        echo "Testing Database Table... ✓ PASS\n";
        $success[] = "Applications table exists";
    } else {
        echo "Testing Database Table... ⚠ WARNING\n";
        $warnings[] = "Applications table not found (will be created on first use)";
    }
} catch (Exception $e) {
    echo "✗ FAIL\n";
    $errors[] = "Database error: " . $e->getMessage();
}

// Test 6: FPDF Library
echo "Testing FPDF Library... ";
if (file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    require_once __DIR__ . '/fpdf/fpdf.php';
    if (class_exists('FPDF')) {
        echo "✓ PASS\n";
        $success[] = "FPDF library is installed";
    } else {
        echo "⚠ WARNING\n";
        $warnings[] = "FPDF library file exists but class not found";
    }
} else {
    echo "⚠ WARNING\n";
    $warnings[] = "FPDF library not installed (PDF generation will not work)";
}

// Test 7: Required Files
echo "Testing Required Files... ";
$requiredFiles = [
    'form.html',
    'submit_form.php',
    'download_admit_card.php',
    'admin-panel.php',
    'database.html',
    'index.html'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $missingFiles[] = $file;
    }
}

if (empty($missingFiles)) {
    echo "✓ PASS\n";
    $success[] = "All required files exist";
} else {
    echo "✗ FAIL\n";
    $errors[] = "Missing files: " . implode(', ', $missingFiles);
}

// Test 8: Curl Extension (for install_fpdf.php)
echo "Testing cURL Extension... ";
if (extension_loaded('curl')) {
    echo "✓ PASS\n";
    $success[] = "cURL extension is loaded (for FPDF installer)";
} else {
    echo "⚠ WARNING\n";
    $warnings[] = "cURL extension not loaded (automatic FPDF install may not work)";
}

// Summary
echo "\n=================================================\n";
echo "  TEST SUMMARY\n";
echo "=================================================\n\n";

echo "✓ Passed: " . count($success) . "\n";
echo "⚠ Warnings: " . count($warnings) . "\n";
echo "✗ Errors: " . count($errors) . "\n\n";

if (!empty($errors)) {
    echo "ERRORS:\n";
    foreach ($errors as $i => $error) {
        echo "  " . ($i + 1) . ". " . $error . "\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "WARNINGS:\n";
    foreach ($warnings as $i => $warning) {
        echo "  " . ($i + 1) . ". " . $warning . "\n";
    }
    echo "\n";
}

if (empty($errors)) {
    echo "=================================================\n";
    echo "  ✓ SYSTEM IS READY!\n";
    echo "=================================================\n\n";
    
    if (!empty($warnings)) {
        echo "Note: There are some warnings. The system will work,\n";
        echo "but some features may be limited.\n\n";
        
        if (in_array("FPDF library not installed (PDF generation will not work)", $warnings)) {
            echo "To install FPDF, run: php install_fpdf.php\n";
            echo "Or download from: http://www.fpdf.org/\n\n";
        }
    }
    
    echo "Next Steps:\n";
    echo "1. Start the server: php -S localhost:8000\n";
    echo "2. Open browser: http://localhost:8000/index.html\n";
    echo "3. Test the application form\n";
    echo "4. Access admin panel (admin/admin123)\n\n";
} else {
    echo "=================================================\n";
    echo "  ✗ SYSTEM HAS ERRORS\n";
    echo "=================================================\n\n";
    echo "Please fix the errors above before using the system.\n\n";
}

echo "For detailed setup instructions, see SETUP.txt\n";
echo "=================================================\n";
?>
