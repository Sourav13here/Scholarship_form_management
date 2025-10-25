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
    // Tighter margins and controlled page breaks to fit one page
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 12);
    $pdf->AddPage();
    
    // Top logos near edges and combined heading between them
    $pdf->SetTextColor(0, 0, 0);
    $logoLeft = __DIR__ . '/images/hfssslogo.png';
    $logoRight = __DIR__ . '/images/logo.png';
    $edgeMargin = 10; // page margins
    $logoW = 24; // logo width
    $logoY = 10; // top y for logos
    $leftX = $edgeMargin;
    $rightX = 210 - $edgeMargin - $logoW; // A4 width 210mm
    if (file_exists($logoLeft)) {
        $pdf->Image($logoLeft, $leftX, $logoY, $logoW);
    }
    if (file_exists($logoRight)) {
        $pdf->Image($logoRight, $rightX, $logoY, $logoW);
    }

    // Combined heading centered between logos
    $textPad = 6; // padding from logos into the text area
    $textX = $leftX + $logoW + $textPad;
    $textW = ($rightX - $textPad) - $textX; // width between logos
    $pdf->SetXY($textX, $logoY + 3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell($textW, 6, 'Holy Flower Senior Secondary School,Scholarship Test', 0, 2, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell($textW, 6, 'In collaboration with Nucleon Coaching Institute, Durgapur', 0, 2, 'C');

    // Move below the header band and print Admit Card line
    $pdf->SetY(max($logoY + $logoW + 4, $pdf->GetY() + 4));
    $pdf->SetFont('Arial', 'I', 13);
    $pdf->Cell(0, 8, 'Admit Card', 0, 1, 'C');
    
    // Add some space
    $pdf->Ln(8);
    
    // Application ID Box
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Application ID: ' . $application['application_id'], 1, 1, 'C', true);
    
    $pdf->Ln(3);
    
    // Applicant Details
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell(0, 10, 'Applicant Information', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(2);
    
    // Create a bordered section
    $startY = $pdf->GetY();
    
    // Add student photo if available
    if (!empty($application['photo'])) {
        $photoPath = __DIR__ . '/uploads/' . $application['photo'];
        if (file_exists($photoPath)) {
            // Position photo in top right corner of the information section
            $photoX = 162;
            $photoY = $startY;
            $photoWidth = 30;
            $photoHeight = 38;
            
            // Add photo border
            $pdf->Rect($photoX, $photoY, $photoWidth, $photoHeight);
            
            // Add photo
            $pdf->Image($photoPath, $photoX + 1, $photoY + 1, $photoWidth - 2, $photoHeight - 2);
        }
    }
    
    // Details (adjust width to accommodate photo on right)
    $detailsWidth = !empty($application['photo']) ? 105 : 0; // Leave space for photo
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Name:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['name'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['name'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Class:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['class'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['class'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'School:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $currentX = $pdf->GetX();
    $currentY = $pdf->GetY();
    if ($detailsWidth > 0) {
        $pdf->MultiCell($detailsWidth, 8, $application['school'], 0, 'L');
    } else {
        $pdf->MultiCell(0, 8, $application['school'], 0, 'L');
    }
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Contact:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['contact'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['contact'], 0, 1);
    }
    
    
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Email:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    if ($detailsWidth > 0) {
        $pdf->Cell($detailsWidth, 8, $application['email'], 0, 1);
    } else {
        $pdf->Cell(0, 8, $application['email'], 0, 1);
    }
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Address:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    if ($detailsWidth > 0) {
        $pdf->MultiCell($detailsWidth, 8, $application['address'], 0, 'L');
    } else {
        $pdf->MultiCell(0, 8, $application['address'], 0, 'L');
    }
    
    $endY = $pdf->GetY();
    $pdf->Rect(10, $startY - 2, 190, $endY - $startY + 4);
    
    $pdf->Ln(5);

    // Single-column layout: Venue & Schedule then Important Instructions
    $contentW = 190; // full content width
    $xLeft = 10;

    // Venue & Schedule
    $pdf->SetX($xLeft);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell($contentW, 6, 'Venue & Schedule', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $labelW = 64; // widen label column to prevent wrapping
    $rowH = 6; // unified row height

    // Helper rows: render label (left) and value (right) with synchronized heights
    // School Name
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'School Name:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, 'Holy Flower Senior Secondary School', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Address
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Address:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, 'Hospital Road, Teok, Jorhat, Assam, Pin-785112', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Scholarship Exam date
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Scholarship Exam date:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, '30th November 2025', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Scholarship Exam time
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Scholarship Exam time:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, '10:00 am', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Reporting time
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Reporting time:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, '9:30 am', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Scholarship Exam duration
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Scholarship Exam duration:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, '2 hours', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Contact number
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Contact number:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, '6003214405, 9101458652', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));
    $pdf->Ln(2);

    // Submission Date (keep on page 1)
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 6, 'Submitted on: ' . date('d-M-Y', strtotime($application['submission_date'])), 0, 1);
    $pdf->Ln(4);

    // Signature Section (two columns: Principal left, Director right)
    $pdf->Ln(8);
    $yStart = $pdf->GetY();
    $signW = 40; // width in mm for both signs
    $signH = 16; // fixed height to standardize alignment
    $captionY = $yStart + $signH + 2; // place caption ~2mm below the image bottom

    // Left: Principal (BBTS Kokrajhar)
    $signPath2 = __DIR__ . '/images/sign2.png';
    $leftX = 14; // add left margin for left signature
    $leftBottom = $yStart;
    if (file_exists($signPath2)) {
        $pdf->Image($signPath2, $leftX, $yStart, $signW, $signH);
        $pdf->SetY($captionY);
        $pdf->SetX($leftX);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($signW, 6, 'Principal', 0, 1, 'C');
        $pdf->SetX($leftX);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($signW, 5, 'Holy Flower Senior Secondary School, Teok', 0, 1, 'C');
        $leftBottom = $captionY + 11; // 6 + 5 lines
    } else {
        // Fallback text if image missing
        $pdf->SetY($yStart + 12);
        $pdf->SetX($leftX);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($signW, 6, 'Principal', 0, 1, 'C');
        $pdf->SetX($leftX);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($signW, 5, 'Holy Flower Senior Secondary School, Teok', 0, 1, 'C');
        $leftBottom = $pdf->GetY();
    }

    // Right: Director (Nucleon)
    $signPath = __DIR__ . '/images/sign.png';
    $rightMargin = 10; // default margin
    $pageWidth = 210; // A4 width
    $rightX = $pageWidth - $rightMargin - $signW - 4; // add right margin for right signature
    $rightBottom = $yStart;
    if (file_exists($signPath)) {
        $pdf->Image($signPath, $rightX, $yStart, $signW, $signH);
        $pdf->SetY($captionY);
        $pdf->SetX($rightX);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($signW, 6, 'Director', 0, 1, 'C');
        $pdf->SetX($rightX);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($signW, 5, 'Nucleon Coaching Institute, Durgapur', 0, 1, 'C');
        $rightBottom = $captionY + 11; // 6 + 5 lines
    } else {
        $pdf->SetY($yStart + 12);
        $pdf->SetX($rightX);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($signW, 6, 'Director', 0, 1, 'C');
        $pdf->SetX($rightX);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($signW, 5, 'Nucleon Coaching Institute, Durgapur', 0, 1, 'C');
        $rightBottom = $pdf->GetY();
    }


    // Move cursor below the lower of the two blocks
    $pdf->SetY(max($leftBottom, $rightBottom) + 4);

    // Important Instructions moved to new page
    $pdf->AddPage();
    $xLeft = 10; $contentW = 190;
    $pdf->SetX($xLeft);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell($contentW, 8, 'Instructions for Candidates', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(2);
    $pdf->MultiCell($contentW, 6,
        '1. Arrival:' . "\n" .
        '- Arrive at least 30 minutes before the scheduled exam time to allow for check-in and seating.' . "\n\n" .
        '2. Identification:' . "\n" .
        '- Bring this admit card along with a valid photo ID proof (Aadhar Card, School ID, Passport, etc.) for verification purposes.' . "\n\n" .
        '3. Materials Allowed:' . "\n" .
        '- Only the following items are permitted in the examination hall:' . "\n" .
        '  - Admit Card' . "\n" .
        '  - Valid Photo ID' . "\n" .
        '  - Blue/Black ballpoint pen' . "\n" .
        '  - Transparent water bottle' . "\n\n" .
        '4. Prohibited Items:' . "\n" .
        '- The following items are strictly prohibited in the examination hall:' . "\n" .
        '  - Electronic devices (mobile phones, smartwatches, tablets, etc.)' . "\n" .
        '  - Study materials (books, notes, etc.)' . "\n" .
        '  - Bags or backpacks' . "\n\n" .
        '5. Exam Format:' . "\n" .
        '- The exam will consist of multiple-choice questions and descriptive questions covering Mathematics & Science.'
    );

    // (Submission date intentionally omitted on second page)

    
    // Move cursor below the lower of the two blocks
    $pdf->SetY(max($leftBottom, $rightBottom) + 4);
    
    // Output PDF
    $pdf->Output('D', 'Admit_Card_' . $application['application_id'] . '.pdf');
    
} catch(PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
