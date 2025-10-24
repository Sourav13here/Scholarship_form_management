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
    
    // Top logos (no colored header)
    $pdf->SetTextColor(0, 0, 0);
    $logoLeft = __DIR__ . '/images/bbtlogo.png';
    $logoRight = __DIR__ . '/images/logo.png';
    if (file_exists($logoLeft)) {
        // x=30mm, y=10mm, width=25mm (height auto)
        $pdf->Image($logoLeft, 30, 10, 25);
    }
    if (file_exists($logoRight)) {
        // x=155mm, y=10mm, width=25mm (height auto)
        $pdf->Image($logoRight, 155, 10, 25);
    }

    // Move Y below logos and add title
    $pdf->SetY(40);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 10, 'Brahma Baba Techno School Scholarship Test', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 8, 'Admit Card', 0, 1, 'C');
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, 'In collaboration with Nucleon Coaching Institute, Durgapur', 0, 1, 'C');
    
    // Add some space
    $pdf->Ln(12);
    
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
    
    // Add student photo if available
    if (!empty($application['photo'])) {
        $photoPath = __DIR__ . '/uploads/' . $application['photo'];
        if (file_exists($photoPath)) {
            // Position photo in top right corner of the information section
            $photoX = 160;
            $photoY = $startY;
            $photoWidth = 35;
            $photoHeight = 45;
            
            // Add photo border
            $pdf->Rect($photoX, $photoY, $photoWidth, $photoHeight);
            
            // Add photo
            $pdf->Image($photoPath, $photoX + 1, $photoY + 1, $photoWidth - 2, $photoHeight - 2);
        }
    }
    
    // Details (adjust width to accommodate photo on right)
    $detailsWidth = !empty($application['photo']) ? 105 : 0; // Leave space for photo
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Name:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['name'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['name'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Class:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['class'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['class'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'School:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $currentX = $pdf->GetX();
    $currentY = $pdf->GetY();
    if ($detailsWidth > 0) {
        $pdf->MultiCell($detailsWidth, 8, $application['school'], 0, 'L');
    } else {
        $pdf->MultiCell(0, 8, $application['school'], 0, 'L');
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Contact:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['contact'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['contact'], 0, 1);
    }
    
    if (!empty($application['alt_contact'])) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 8, 'Alt. Contact:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        if ($detailsWidth > 0) {
            $pdf->Cell($detailsWidth, 8, $application['alt_contact'], 0, 1);
        } else {
            $pdf->Cell(0, 8, $application['alt_contact'], 0, 1);
        }
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Email:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['email'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['email'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 8, 'Address:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    if ($detailsWidth > 0) {
        $pdf->MultiCell($detailsWidth, 8, $application['address'], 0, 'L');
    } else {
        $pdf->MultiCell(0, 8, $application['address'], 0, 'L');
    }
    
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
                           '4. For any queries, contact: ');

    // Signature Section (right-aligned)
    $pdf->Ln(8);
    $signPath = __DIR__ . '/images/sign.png';
    if (file_exists($signPath)) {
        $signWidth = 45; // mm
        $rightMargin = 10; // default FPDF margin
        $pageWidth = 210; // A4 portrait width in mm
        $x = $pageWidth - $rightMargin - $signWidth; // right align
        $y = $pdf->GetY();
        $pdf->Image($signPath, $x, $y, $signWidth);
        $pdf->SetY($y + 30); // space below image
        // Center the text under the image by constraining cell width to image width
        $pdf->SetX($x);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($signWidth, 6, 'Director', 0, 1, 'C');
        $pdf->SetX($x);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($signWidth, 5, 'Nucleon Coaching Institute, Durgapur', 0, 1, 'C');
    } else {
        $pdf->Ln(12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, 'Director', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, 'Nucleon Coaching Institute, Durgapur', 0, 1, 'R');
    }
    
    // Output PDF
    $pdf->Output('D', 'Admit_Card_' . $application['application_id'] . '.pdf');
    
} catch(PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
