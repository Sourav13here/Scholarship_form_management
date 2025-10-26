<?php
session_start();
require_once 'config.php';

// Ensure only admins can perform this action
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-panel.php?status=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-panel.php?status=error');
    exit;
}

// CSRF check
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: admin-panel.php?status=error');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Location: admin-panel.php?status=error');
    exit;
}

try {
    $db = getDBConnection();

    // Fetch application to get photo filename
    $stmt = $db->prepare('SELECT photo FROM applications WHERE id = ?');
    $stmt->execute([$id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        header('Location: admin-panel.php?status=error');
        exit;
    }

    $db->beginTransaction();

    // Delete the record
    $del = $db->prepare('DELETE FROM applications WHERE id = ?');
    $del->execute([$id]);

    $db->commit();

    // Attempt to delete the uploaded photo if present
    if (!empty($app['photo'])) {
        $photoPath = __DIR__ . '/uploads/' . basename($app['photo']);
        if (is_file($photoPath)) {
            @unlink($photoPath);
        }
    }

    header('Location: admin-panel.php?status=deleted');
    exit;
} catch (Exception $e) {
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    header('Location: admin-panel.php?status=error');
    exit;
}
