<?php
header('Content-Type: application/json');
require_once 'config.php';

// Initialize database
initializeDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $requiredFields = ['name', 'class', 'school', 'address', 'contact', 'declaration'];
    $missingFields = [];
    
    // Create uploads directory if it doesn't exist
    $uploadsDir = __DIR__ . '/uploads';
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }
    
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
    
    // Validate email only if provided (optional field)
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
    
    // Validate photo upload
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'Please upload a photo'
        ]);
        exit;
    }
    
    // Validate photo file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $fileType = $_FILES['photo']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid photo format. Only JPG, JPEG, and PNG are allowed'
        ]);
        exit;
    }
    
    // Validate photo file size (2MB max)
    if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'Photo size must be less than 2MB'
        ]);
        exit;
    }
    
    // Generate unique application ID
    $applicationId = 'NCI' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Handle photo upload
    $photoExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $photoFileName = $applicationId . '_' . time() . '.' . $photoExtension;
    $photoPath = $uploadsDir . '/' . $photoFileName;
    
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload photo. Please try again'
        ]);
        exit;
    }
    
    try {
        $db = getDBConnection();
        
        // Check if email already exists (only when provided)
        if ($email !== '') {
            $checkStmt = $db->prepare("SELECT id FROM applications WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'An application with this email already exists'
                ]);
                exit;
            }
        }
        
        // Insert application
        $stmt = $db->prepare("
            INSERT INTO applications (
                application_id, name, class, school, address, 
                contact, alt_contact, email, photo, achievements, declaration
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $applicationId,
            $_POST['name'],
            $_POST['class'],
            $_POST['school'],
            $_POST['address'],
            $_POST['contact'],
            $_POST['alt_contact'] ?? '',
            $email,
            $photoFileName,
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
