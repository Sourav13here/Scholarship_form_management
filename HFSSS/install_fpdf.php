<?php
/**
 * FPDF Installation Script
 * This script downloads and installs FPDF library automatically
 */

echo "Installing FPDF Library...\n\n";

// Create fpdf directory if it doesn't exist
if (!file_exists('fpdf')) {
    mkdir('fpdf', 0755, true);
    echo "✓ Created fpdf directory\n";
}

// Download FPDF
$fpdfUrl = 'https://www.fpdf.org/en/dl.php?v=186&f=zip';
$zipFile = 'fpdf.zip';

echo "Downloading FPDF from $fpdfUrl...\n";

$ch = curl_init($fpdfUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$zipContent = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $zipContent) {
    file_put_contents($zipFile, $zipContent);
    echo "✓ Downloaded FPDF\n";
    
    // Extract zip
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo('.');
        $zip->close();
        echo "✓ Extracted FPDF\n";
        
        // Move fpdf.php to fpdf folder
        if (file_exists('fpdf185/fpdf.php')) {
            copy('fpdf185/fpdf.php', 'fpdf/fpdf.php');
            echo "✓ Installed fpdf.php\n";
            
            // Copy font files if needed
            if (is_dir('fpdf185/font')) {
                if (!file_exists('fpdf/font')) {
                    mkdir('fpdf/font', 0755, true);
                }
                $files = glob('fpdf185/font/*');
                foreach ($files as $file) {
                    copy($file, 'fpdf/font/' . basename($file));
                }
                echo "✓ Installed font files\n";
            }
            
            // Clean up
            unlink($zipFile);
            deleteDirectory('fpdf185');
            echo "✓ Cleaned up temporary files\n";
            
            echo "\n✅ FPDF installed successfully!\n";
            echo "\nYou can now use the scholarship application system.\n";
            echo "Visit form.html to get started.\n";
        } else {
            echo "❌ Error: fpdf.php not found in extracted files\n";
        }
    } else {
        echo "❌ Error: Could not extract zip file\n";
    }
} else {
    echo "❌ Error: Could not download FPDF (HTTP Code: $httpCode)\n";
    echo "\nManual Installation:\n";
    echo "1. Download FPDF from: http://www.fpdf.org/\n";
    echo "2. Extract and copy fpdf.php to the fpdf/ folder\n";
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) return;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}
?>
