<?php
session_start();
require_once 'config.php';

// Simple authentication (you should enhance this with proper password hashing)
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD = 'admin123'; // Change this!

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['username'] === $ADMIN_USERNAME && $_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        // Redirect to avoid form resubmission on refresh (PRG pattern)
        header('Location: admin-panel.php');
        exit;
    } else {
        $loginError = 'Invalid credentials';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-panel.php');
    exit;
}

// Check if logged in
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - BBTS Scholarship Test</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

        .alert {
            margin: 0 0 20px 0;
            padding: 12px 16px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-left: 4px solid #dc3545; }

        .delete-btn {
            background: #c62828;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
        .delete-btn:hover { background: #b71c1c; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #0d47a1;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-container {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                width: 100%;
                max-width: 400px;
            }
            h2 {
                color: #0d47a1;
                margin-bottom: 30px;
                text-align: center;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #333;
            }
            input {
                width: 100%;
                padding: 12px 15px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 14px;
            }
            input:focus {
                outline: none;
                border-color: #0d47a1;
            }
            button {
                width: 100%;
                padding: 12px;
                background: #0d47a1;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
            }
            button:hover {
                opacity: 0.9;
            }
            .error {
                color: #e74c3c;
                margin-bottom: 15px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Admin Login</h2>
            <?php if (isset($loginError)): ?>
                <div class="error"><?php echo $loginError; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Ensure CSRF token exists for admin actions
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get statistics
$db = getDBConnection();
$totalApplications = $db->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$todayApplications = $db->query("SELECT COUNT(*) FROM applications WHERE DATE(submission_date) = DATE('now')")->fetchColumn();

// Get all applications
$applications = $db->query("SELECT * FROM applications ORDER BY submission_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BBTS Scholarship Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .header {
            background: #0d47a1;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 24px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border: 1px solid white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #0d47a1;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }

        .applications-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }

        .section-header h2 {
            color: #333;
            font-size: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #0d47a1;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .download-btn {
            background: #0d47a1;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
        }

        .download-btn:hover {
            background: #1565c0;
        }

        /* Alerts (status banners) */
        .alert {
            margin: 0 0 20px 0;
            padding: 12px 16px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-left: 4px solid #dc3545; }

        /* Delete button styling */
        button.delete-btn, .delete-btn {
            background: #c62828;
            color: white;
            padding: 8px 14px;
            border: none !important;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
            -webkit-appearance: none;
            appearance: none;
            margin-left: 8px;
        }
        button.delete-btn:hover, .delete-btn:hover { background: #b71c1c; }

        .export-btn {
            background: #217346;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s, background 0.3s;
        }

        .export-btn:hover {
            background: #1a5c37;
            transform: translateY(-2px);
        }

        .export-btn:before {
            content: "📗 ";
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #999;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }

        .section-header h2 {
            color: #333;
            font-size: 20px;
            margin: 0;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .section-header {
                flex-direction: column;
                gap: 15px;
            }

            .export-btn {
                width: 100%;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nucleon Scholarship - Admin Panel</h1>
        <a href="?logout=1" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-success">Application deleted successfully.</div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-error">Failed to delete application. Please try again.</div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Applications</h3>
                <div class="number"><?php echo $totalApplications; ?></div>
            </div>
            <div class="stat-card">
                <h3>Today's Applications</h3>
                <div class="number"><?php echo $todayApplications; ?></div>
            </div>
        </div>

        <div class="applications-section">
            <div class="section-header">
                <h2>All Applications</h2>
                <a href="export_excel.php" class="export-btn excel">Export to Excel</a>
            </div>
            <div class="table-container">
                <?php if (count($applications) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>School</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Submission Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($app['application_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['name']); ?></td>
                            <td><?php echo htmlspecialchars($app['class']); ?></td>
                            <td><?php echo htmlspecialchars($app['school']); ?></td>
                            <td><?php echo htmlspecialchars($app['contact']); ?></td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td><?php echo date('d-M-Y H:i', strtotime($app['submission_date'])); ?></td>
                            <td>
                                <a href="download_admit_card.php?id=<?php echo $app['id']; ?>" class="download-btn">Download PDF</a>
                                <form method="POST" action="delete_application.php" style="display:inline-block; margin-left:8px;" onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.');">
                                    <input type="hidden" name="id" value="<?php echo (int)$app['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-data">No applications found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
