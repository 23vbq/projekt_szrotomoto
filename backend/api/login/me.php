<?php
require_once __DIR__ . '/../../utils_loader.php';

// Return lightweight info about current session.
// Response shape:
// { authenticated: bool, user_id?: int, user_name?: string }

$userId = Session::getUserId();
// Log some diagnostic info to the PHP error log to help debugging auth issues.
try {
    $cookieSession = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : null;
    $currentSessionId = session_id();
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? null;
    error_log('me.php request: ' . json_encode([
        'remote_addr' => $clientIp,
        'cookie_php_session' => $cookieSession,
        'session_id' => $currentSessionId,
        'user_id_before' => $userId
    ]));
} catch (Throwable $e) {
    // Ignore logging errors
}
if ($userId === null) {
    // Log negative result
    error_log('me.php response: unauthenticated');
    Response::json(['authenticated' => false]);
} else {
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
    // Log positive result
    error_log('me.php response: authenticated user_id=' . (int)$userId . ' name=' . ($userName ?? ''));
    Response::json([
        'authenticated' => true,
        'user_id' => $userId,
        'user_name' => $userName
    ]);
}
