<?php
// Session management with serverless /tmp compatibility
if (session_status() === PHP_SESSION_NONE) {
    if (getenv('VERCEL') || !empty($_ENV['VERCEL']) || !is_writable(session_save_path() ?: '/')) {
        @ini_set('session.save_path', '/tmp');
    }
    session_start();
}

// BASE_URL definition
if (!defined('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL') ?: '');
}

const STATUSES   = ['Pending', 'In Progress', 'Resolved', 'Rejected'];
const CATEGORIES = ['Service', 'Billing', 'Technical', 'Staff Behaviour', 'Other'];

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function require_login(string $role) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function badge(string $status): string {
    $cls = strtolower(str_replace(' ', '-', $status));
    return '<span class="badge ' . $cls . '">' . e($status) . '</span>';
}

/**
 * Initialize SQLite schema and default demo data
 */
function init_sqlite_db(PDO $pdo) {
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT "user",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS complaints (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            category TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT "Pending",
            admin_remark TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TRIGGER IF NOT EXISTS trigger_complaints_updated_at
        AFTER UPDATE ON complaints
        BEGIN
            UPDATE complaints SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
        END;
    ');

    // Check if admin exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute(['admin@example.com']);
    if ((int)$stmt->fetchColumn() === 0) {
        $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insert = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $insert->execute(['Administrator', 'admin@example.com', $admin_hash, 'admin']);
        $insert->execute(['Smit Akhja', 'smit@example.com', $admin_hash, 'user']);

        // Seed initial sample complaints for a realistic presentation
        $c_stmt = $pdo->prepare('INSERT INTO complaints (user_id, title, category, description, status, admin_remark, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $c_stmt->execute([
            2,
            'Wi-Fi connection drops in Computer Lab 3',
            'Technical',
            'Wi-Fi access point in Computer Lab 3 frequently disconnects during practical lab sessions. Speed drops below 1 Mbps.',
            'In Progress',
            'Network technician assigned to inspect router AP-03 in Lab 3.',
            date('Y-m-d H:i:s', strtotime('-2 days'))
        ]);
        $c_stmt->execute([
            2,
            'Hostel Block B Water Filter Maintenance',
            'Service',
            'The drinking water filter on 2nd floor of Block B requires servicing and cartridge change.',
            'Resolved',
            'Cartridge replaced and TDS tested within normal limits.',
            date('Y-m-d H:i:s', strtotime('-5 days'))
        ]);
        $c_stmt->execute([
            2,
            'Semester Exam Fee Payment Receipt Discrepancy',
            'Billing',
            'Fee payment of ₹1,500 was successfully debited from bank account, but student portal status still shows fee unpaid.',
            'Pending',
            null,
            date('Y-m-d H:i:s', strtotime('-1 hours'))
        ]);
    }
}

// Database Connection Logic:
// 1) If DB_HOST is set to a remote MySQL host, try connecting to MySQL
// 2) Otherwise (or on fallback), use SQLite (zero-config, instant, 100% reliable on Vercel)
$pdo = null;
$is_vercel = !empty(getenv('VERCEL')) || !empty($_ENV['VERCEL']) || !empty(getenv('NOW_REGION'));
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME') ?: 'complaint_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_port = getenv('DB_PORT') ?: '3306';

if (!empty($db_host) && (!$is_vercel || ($db_host !== 'localhost' && $db_host !== '127.0.0.1'))) {
    try {
        $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 3,
        ]);
    } catch (Throwable $e) {
        $pdo = null; // fallback to SQLite
    }
}

// Fallback to SQLite (seamless zero-config for Vercel and local development)
if (!$pdo) {
    try {
        $is_tmp = ($is_vercel || !is_writable(__DIR__));
        $db_dir = $is_tmp ? '/tmp' : __DIR__;
        $db_file = $db_dir . '/complaint_system.sqlite';

        // Preload bundled database if available
        if ($is_tmp && !file_exists($db_file) && file_exists(__DIR__ . '/complaint_system.sqlite')) {
            @copy(__DIR__ . '/complaint_system.sqlite', $db_file);
        }

        $need_init = !file_exists($db_file) || filesize($db_file) === 0;

        $pdo = new PDO('sqlite:' . $db_file, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON;');

        if ($need_init) {
            init_sqlite_db($pdo);
        }
    } catch (Throwable $e) {
        die('Database initialization error: ' . htmlspecialchars($e->getMessage()));
    }
}
