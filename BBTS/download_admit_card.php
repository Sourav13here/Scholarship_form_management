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
    $logoLeft = __DIR__ . '/images/bbtlogo.png';
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
    $pdf->Cell($textW, 6, 'Brahma Baba Techno School Scholarship Test', 0, 2, 'C');
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
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, 'Brahma Baba Techno School Kokrajhar', 0, 'L');
    $pdf->SetY(max($yAfterLabel, $pdf->GetY()));

    // Address
    $y = $pdf->GetY();
    $pdf->SetX($xLeft); $pdf->SetFont('Arial', 'B', 12); $pdf->MultiCell($labelW, $rowH, 'Address:', 0, 'L');
    $yAfterLabel = $pdf->GetY();
    $pdf->SetXY($xLeft + $labelW, $y); $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW - $labelW, $rowH, 'Janagaon, Titaguri Part-II, Kokrajhar, BTR, Assam, Pin- 783370', 0, 'L');
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
        $pdf->Cell($signW, 5, 'Brahma Baba Techno School, Kokrajhar', 0, 1, 'C');
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
        $pdf->Cell($signW, 5, 'Brahma Baba Techno School, Kokrajhar', 0, 1, 'C');
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
    // 1. Arrival
    $pdf->SetFont('Arial', 'B', 12); $pdf->Cell($contentW, 6, '1. Arrival', 0, 1);
    $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW, 6, 'Arrive 30 minutes early for check-in and seating.'); $pdf->Ln(1);

    // 2. Identification
    $pdf->SetFont('Arial', 'B', 12); $pdf->Cell($contentW, 6, '2. Identification', 0, 1);
    $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW, 6, 'Carry this Admit Card and a valid photo ID (Aadhar/School ID/Passport). No entry without ID.'); $pdf->Ln(1);

    // 3. Permitted Items
    $pdf->SetFont('Arial', 'B', 12); $pdf->Cell($contentW, 6, '3. Permitted Items', 0, 1);
    $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW, 6, ' Admit Card, Valid Photo ID, Ballpoint Pen (Blue/Black), Transparent Water Bottle.'); $pdf->Ln(1);

    // 4. Prohibited Items
    $pdf->SetFont('Arial', 'B', 12); $pdf->Cell($contentW, 6, '4. Prohibited Items', 0, 1);
    $pdf->SetFont('Arial', '', 12); $pdf->MultiCell($contentW, 6, ' Electronic devices, study materials, bags/purses/backpacks.'); $pdf->Ln(1);

    // 5. Exam Format
    $pdf->SetFont('Arial', 'B', 12); $pdf->Cell($contentW, 6, '5. Exam Format', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell($contentW, 6, 'MCQs + Descriptive from Mathematics & Science. Both subjects have equal weightage.');
    $pdf->MultiCell($contentW, 6, 'Duration: 120 Minutes');
    $pdf->Ln(2);

    // Exam Format Table (2 rows, 7 equal-width columns)
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetFillColor(245, 245, 245);
    // Equal column widths summing to 190mm
    $colW = [27, 27, 27, 27, 27, 27, 28];
    $headers = ['Section', 'Type', 'Total Questions', 'Marks per Question', 'Negative Marking', 'Total Marks', 'Duration'];
    // Header row with synchronized height
    $yHeader = $pdf->GetY();
    $x = $xLeft;
    $headerBottoms = [];
    $xPositions = [];
    for ($i = 0; $i < count($headers); $i++) {
        $pdf->SetXY($x, $yHeader);
        $pdf->SetFont('Arial', 'B', 12);
        // Draw text without borders to avoid inner boxes
        $pdf->MultiCell($colW[$i], 6, $headers[$i], 0, 'C', true);
        $headerBottoms[$i] = $pdf->GetY();
        $xPositions[$i] = $x;
        $x += $colW[$i];
    }
    $yHeaderMax = max($headerBottoms);
    // Draw a single rectangle per header cell for the full height
    for ($i = 0; $i < count($headers); $i++) {
        $pdf->Rect($xPositions[$i], $yHeader, $colW[$i], $yHeaderMax - $yHeader, 'D');
    }
    $pdf->SetY($yHeaderMax);

    // Data row with wrapping per column and synchronized height
    $pdf->SetFont('Arial', '', 12);
    $row = ['Mathematics & Science', 'MCQs + Descriptive', '60 (MCQs)', '+4', '-1', '240', '120 Minutes'];
    $yRow = $pdf->GetY();
    $x = $xLeft; // align with left margin
    $rowBottoms = [];
    $rowX = [];
    for ($i = 0; $i < count($row); $i++) {
        $pdf->SetXY($x, $yRow);
        // Draw text without borders
        $pdf->MultiCell($colW[$i], 6, $row[$i], 0, 'C');
        $rowBottoms[$i] = $pdf->GetY();
        $rowX[$i] = $x;
        $x += $colW[$i];
    }
    $yRowMax = max($rowBottoms);
    // Draw a single rectangle per data cell for the full height
    for ($i = 0; $i < count($row); $i++) {
        $pdf->Rect($rowX[$i], $yRow, $colW[$i], $yRowMax - $yRow, 'D');
    }
    $pdf->SetY($yRowMax);
    $pdf->Ln(6);

    // Continue with remaining sections
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell($contentW, 6,
        '6. Conduct' . "\n" .
        'Maintain silence and follow invigilators instructions. Cheating leads to disqualification.' . "\n\n" .
        '7. Completion of Exam' . "\n" .
        'Review answers and wait for permission before leaving.' . "\n\n" .
        '8. Post-Exam' . "\n" .
        'Results on 15th December, 2025 on the BBTS Centre website; also via email/WhatsApp.' . "\n\n" .
        'For any queries or assistance, please contact:' . "\n" .
        ' Phone: 6003214405, 9101458652' . "\n" .
        ' Email: me.dibyendu92@gmail.com'
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
