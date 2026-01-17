<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::start();

$isAuthenticated = isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true;
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

Response::json([
    'authenticated' => $isAuthenticated,
    'user_id' => $userId,
    'user_name' => $userName
]);

