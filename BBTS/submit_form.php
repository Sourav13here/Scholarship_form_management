<?php
header('Content-Type: application/json');
require_once 'config.php';

// Initialize database
initializeDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $requiredFields = ['name', 'class', 'school', 'address', 'contact', 'email', 'declaration'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill all required fields: ' . implode(', ', $missingFields)
        ]);
        exit;
    }
    
    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit;
    }
    
    // Validate contact number
    if (!preg_match('/^[0-9]{10}$/', $_POST['contact'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Contact number must be 10 digits'
        ]);
        exit;
    }
    
    // Generate unique application ID
    $applicationId = 'NCI' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    try {
        $db = getDBConnection();
        
        // Check if email already exists
        $checkStmt = $db->prepare("SELECT id FROM applications WHERE email = ?");
        $checkStmt->execute([$_POST['email']]);
        if ($checkStmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'An application with this email already exists'
            ]);
            exit;
        }
        
        // Insert application
        $stmt = $db->prepare("
            INSERT INTO applications (
                application_id, name, class, school, address, 
                contact, alt_contact, email, achievements, declaration
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $applicationId,
            $_POST['name'],
            $_POST['class'],
            $_POST['school'],
            $_POST['address'],
            $_POST['contact'],
            $_POST['alt_contact'] ?? '',
            $_POST['email'],
            $_POST['achievements'] ?? '',
            1
        ]);
        
        $insertedId = $db->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully! Your Application ID is: ' . $applicationId,
            'application_id' => $insertedId
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
