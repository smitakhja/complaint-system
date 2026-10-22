<?php
/**
 * Vercel Front Controller / Router for PHP Serverless Execution
 */

// Mark environment as Vercel
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';

// Ensure sessions use /tmp directory
@ini_set('session.save_path', '/tmp');

$uri  = $_SERVER['REQUEST_URI'] ?? '/';
$path = strtok($uri, '?');
$root = dirname(__DIR__);

// Populate $_GET from QUERY_STRING if not already set
if (empty($_GET) && !empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $_GET);
}

// Serve static assets directly if they exist
$staticFile = $root . $path;
if (!empty($path) && $path !== '/' && file_exists($staticFile) && is_file($staticFile)) {
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'json'  => 'application/json',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        header('Cache-Control: public, max-age=86400');
        readfile($staticFile);
        exit;
    }
}

// Standard routing map
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
    '/admin/dashboard'     => 'admin/dashboard.php',
    '/admin/update.php'    => 'admin/update.php',
    '/admin/update'        => 'admin/update.php',
    '/user/dashboard.php'  => 'user/dashboard.php',
    '/user/dashboard'      => 'user/dashboard.php',
    '/user/submit.php'     => 'user/submit.php',
    '/user/submit'         => 'user/submit.php',
    '/user/view.php'       => 'user/view.php',
    '/user/view'           => 'user/view.php',
];

$file = $map[$path] ?? null;

// Fallback search in root directory for matching PHP file
if (!$file) {
    $candidate = ltrim($path, '/');
    if (file_exists($root . '/' . $candidate) && substr($candidate, -4) === '.php') {
        $file = $candidate;
    }
}

if ($file && file_exists($root . '/' . $file)) {
    $_SERVER['SCRIPT_NAME']     = '/' . $file;
    $_SERVER['SCRIPT_FILENAME'] = $root . '/' . $file;
    $_SERVER['PHP_SELF']        = '/' . $file;

    chdir(dirname($root . '/' . $file));
    include $root . '/' . $file;
    exit;
}

// 404 handler
http_response_code(404);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>404 - Page Not Found</title>
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
  <div class="card narrow" style="margin-top: 60px; text-align: center;">
    <h2>404 - Page Not Found</h2>
    <p>The page <code><?= htmlspecialchars($path) ?></code> was not found.</p>
    <a class="btn" href="/">Return to Home</a>
  </div>
</body>
</html>
