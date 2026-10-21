<?php
/**
 * Vercel Front Controller / Router
 * Routes all requests to the correct PHP file in project root.
 */

// Use /tmp for sessions on Vercel (writable directory)
ini_set('session.save_path', '/tmp');

$uri  = $_SERVER['REQUEST_URI'] ?? '/';
$path = strtok($uri, '?');       // strip query string
$root = dirname(__DIR__);        // project root (one level up from api/)

// --- Route map ---
$map = [
    '/'                    => 'index.php',
    '/index.php'           => 'index.php',
    '/login.php'           => 'login.php',
    '/login'               => 'login.php',
    '/register.php'        => 'register.php',
    '/register'            => 'register.php',
    '/logout.php'          => 'logout.php',
    '/logout'              => 'logout.php',
    '/admin/dashboard.php' => 'admin/dashboard.php',
    '/admin/update.php'    => 'admin/update.php',
    '/user/dashboard.php'  => 'user/dashboard.php',
    '/user/submit.php'     => 'user/submit.php',
    '/user/view.php'       => 'user/view.php',
];

// --- Serve static assets (CSS, JS, images) ---
$staticFile = $root . $path;
if (file_exists($staticFile) && is_file($staticFile)) {
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        readfile($staticFile);
        exit;
    }
}

// --- Route PHP files ---
$file = $map[$path] ?? null;

// If not in map, try matching directly (e.g. /admin/update.php)
if (!$file) {
    $candidate = ltrim($path, '/');
    if (file_exists($root . '/' . $candidate) && substr($candidate, -4) === '.php') {
        $file = $candidate;
    }
}

if ($file && file_exists($root . '/' . $file)) {
    // Set globals so included files behave as if called directly
    $_SERVER['SCRIPT_NAME']     = '/' . $file;
    $_SERVER['SCRIPT_FILENAME'] = $root . '/' . $file;
    $_SERVER['PHP_SELF']        = '/' . $file;

    // Change working directory so relative requires work
    chdir(dirname($root . '/' . $file));

    include $root . '/' . $file;
    exit;
}

// --- 404 ---
http_response_code(404);
echo '<h1>404 - Page Not Found</h1>';
echo '<p>Path: ' . htmlspecialchars($path) . '</p>';
