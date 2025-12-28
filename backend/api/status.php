<?php
require_once __DIR__ . '/../utils_loader.php';

$currentTimeFromServer = new \DateTime();

$db = new Database();
$stmt = $db->getPdo()->prepare("SELECT NOW()");
$stmt->execute();

$currentTimeFromDb = $stmt->fetchColumn();

Response::json([
    'status' => 'ok',
    'current_time' => $currentTimeFromServer->format('Y-m-d H:i:s'),
    'current_time_from_db' => $currentTimeFromDb
]);