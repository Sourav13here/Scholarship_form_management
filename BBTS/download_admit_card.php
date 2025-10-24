<?php
require_once 'config.php';
require_once 'fpdf/fpdf.php';

if (!isset($_GET['id'])) {
    die('Invalid request');
}

$id = $_GET['id'];

try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        die('Application not found');
    }
    
    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Header with gradient effect (simulated with colors)
    $pdf->SetFillColor(102, 126, 234);
    $pdf->Rect(0, 0, 210, 50, 'F');
    
    // Title
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 15, '', 0, 1);
    $pdf->Cell(0, 10, 'Nucleon Coaching Institute, Durgapur', 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 8, 'Scholarship Admit Card', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, 'In collaboration with Holyflower Senior Secondary School Teok', 0, 1, 'C');
    
    // Reset text color
    $pdf->SetTextColor(0, 0, 0);
    
    // Add some space
    $pdf->Ln(15);
    
    // Application ID Box
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Application ID: ' . $application['application_id'], 1, 1, 'C', true);
    
    $pdf->Ln(5);
    
    // Applicant Details
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell(0, 10, 'Applicant Information', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    
    $pdf->SetFont('Arial', '', 11);
    $pdf->Ln(2);
    
    // Create a bordered section
    $startY = $pdf->GetY();
    
    // Details
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Name:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, $application['name'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Class:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, $application['class'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'School:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 8, $application['school'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Contact:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, $application['contact'], 0, 1);
    
    if (!empty($application['alt_contact'])) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 8, 'Alt. Contact:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 8, $application['alt_contact'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Email:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, $application['email'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Address:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 8, $application['address'], 0, 1);
    
    $endY = $pdf->GetY();
    $pdf->Rect(10, $startY - 2, 190, $endY - $startY + 4);
    
    $pdf->Ln(5);
    
    // Achievements
    if (!empty($application['achievements'])) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(102, 126, 234);
        $pdf->Cell(0, 10, 'Achievements', 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 6, $application['achievements'], 0, 1);
        $pdf->Ln(3);
    }
    
    // Submission Date
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, 'Submitted on: ' . date('d-M-Y', strtotime($application['submission_date'])), 0, 1);
    
    $pdf->Ln(10);
    
    // Footer
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell(0, 6, 'Important Instructions:', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, '1. Please bring this admit card on the day of the scholarship test.' . "\n" .
                           '2. Carry a valid photo ID proof along with this admit card.' . "\n" .
                           '3. Report to the examination center 30 minutes before the scheduled time.' . "\n" .
                           '4. For any queries, contact: info@nucleoncoaching.com');
    
    // Output PDF
    $pdf->Output('D', 'Admit_Card_' . $application['application_id'] . '.pdf');
    
} catch(PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
