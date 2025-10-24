<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-panel.php');
    exit;
}

// Get all applications
$db = getDBConnection();
$applications = $db->query("SELECT * FROM applications ORDER BY submission_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=scholarship_applications_' . date('Y-m-d_H-i-s') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for proper Excel UTF-8 support
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, array(
    'Application ID',
    'Name',
    'Class',
    'School',
    'Address',
    'Contact Number',
    'Alternative Number',
    'Email',
    'Achievements',
    'Submission Date',
   
));

// Add data rows
foreach ($applications as $app) {
    fputcsv($output, array(
        $app['application_id'],
        $app['name'],
        $app['class'],
        $app['school'],
        $app['address'],
        $app['contact'],
        $app['alt_contact'] ?? '',
        $app['email'],
        $app['achievements'] ?? '',
        date('Y-m-d H:i', strtotime($app['submission_date']))
    ));
}

fclose($output);
exit;
?>
