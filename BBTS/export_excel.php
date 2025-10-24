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

// Set headers for Excel download
$filename = 'scholarship_applications_' . date('Y-m-d_H-i-s') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Start output
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Applications</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th { background-color: #0d47a1; color: white; font-weight: bold; padding: 10px; border: 1px solid #ddd; text-align: left; }';
echo 'td { padding: 8px; border: 1px solid #ddd; }';
echo 'tr:nth-child(even) { background-color: #f8f9fa; }';
echo '.header-row { background-color: #0d47a1; color: white; font-size: 16px; font-weight: bold; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Title
echo '<table>';
echo '<tr class="header-row"><td colspan="10" style="text-align: center; padding: 15px; font-size: 18px;">Nucleon Coaching Institute - Scholarship Applications</td></tr>';
echo '<tr><td colspan="10" style="text-align: center; padding: 5px; font-size: 12px;">Generated on: ' . date('d-M-Y H:i:s') . '</td></tr>';
echo '<tr><td colspan="10" style="padding: 5px;"></td></tr>'; // Empty row

// Headers
echo '<tr>';
echo '<th>Application ID</th>';
echo '<th>Name</th>';
echo '<th>Class</th>';
echo '<th>School</th>';
echo '<th>Address</th>';
echo '<th>Contact Number</th>';
echo '<th>Alternative Number</th>';
echo '<th>Email</th>';
echo '<th>Achievements</th>';
echo '<th>Submission Date</th>';
echo '</tr>';

// Data rows
foreach ($applications as $app) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($app['application_id']) . '</td>';
    echo '<td>' . htmlspecialchars($app['name']) . '</td>';
    echo '<td>' . htmlspecialchars($app['class']) . '</td>';
    echo '<td>' . htmlspecialchars($app['school']) . '</td>';
    echo '<td>' . htmlspecialchars($app['address']) . '</td>';
    echo '<td>' . htmlspecialchars($app['contact']) . '</td>';
    echo '<td>' . htmlspecialchars($app['alt_contact'] ?? '-') . '</td>';
    echo '<td>' . htmlspecialchars($app['email']) . '</td>';
    echo '<td>' . htmlspecialchars($app['achievements'] ?? '-') . '</td>';
    echo '<td>' . date('Y-m-d H:i', strtotime($app['submission_date'])) . '</td>';
    echo '</tr>';
}

// Summary row
echo '<tr><td colspan="10" style="padding: 5px;"></td></tr>'; // Empty row
echo '<tr style="background-color: #e9ecef; font-weight: bold;">';
echo '<td colspan="9" style="text-align: right; padding: 10px;">Total Applications:</td>';
echo '<td style="padding: 10px;">' . count($applications) . '</td>';
echo '</tr>';

echo '</table>';
echo '</body>';
echo '</html>';

exit;
?>
