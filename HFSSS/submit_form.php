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
    
    // Generate sequential application ID: NCI + YYYY + 4-digit counter (0001...)
    $year = date('Y');
    $prefix = 'HFS' . $year;
    $db = getDBConnection();
    $db->beginTransaction();
    try {
        $stmtSeq = $db->prepare("SELECT MAX(CAST(SUBSTR(application_id, -4) AS INTEGER)) AS last_seq FROM applications WHERE application_id LIKE ?");
        $stmtSeq->execute([$prefix . '%']);
        $rowSeq = $stmtSeq->fetch(PDO::FETCH_ASSOC);
        $lastSeq = isset($rowSeq['last_seq']) ? (int)$rowSeq['last_seq'] : 0;
        $nextSeq = $lastSeq + 1;
        if ($nextSeq > 9999) { throw new Exception('Application sequence limit reached for year ' . $year); }
        $applicationId = $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to generate application ID: ' . $e->getMessage()
        ]);
        exit;
    }
    
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
        
        // Check if email already exists (only when provided)
        if ($email !== '') {
            $checkStmt = $db->prepare("SELECT id FROM applications WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'An application with this email already exists'
                ]);
                $db->rollBack();
                exit;
            }
        }

        // Enforce unique contact number
        $checkContact = $db->prepare("SELECT id FROM applications WHERE contact = ?");
        $checkContact->execute([$_POST['contact']]);
        if ($checkContact->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'An application with this contact number already exists'
            ]);
            $db->rollBack();
            exit;
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
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully! Your Application ID is: ' . $applicationId,
            'application_id' => $insertedId
        ]);
        
    } catch(PDOException $e) {
        if ($db->inTransaction()) { $db->rollBack(); }
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
