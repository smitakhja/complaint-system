<?php
session_start();

// Railway / production env vars, fallback to localhost for local dev
$db_host = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
$db_port = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';
$db_name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'complaint_db';
$db_user = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS')     ?: '';

// On Railway, BASE_URL is '' (root), locally it's '/complaint-system'
define('BASE_URL', getenv('RAILWAY_ENVIRONMENT') ? '' : '/complaint-system');

$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

const STATUSES = ['Pending', 'In Progress', 'Resolved', 'Rejected'];
const CATEGORIES = ['Service', 'Billing', 'Technical', 'Staff Behaviour', 'Other'];

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

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
