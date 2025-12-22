<?php
require_once 'utils_loader.php';

$db = new Database();
$stmt = $db->getPdo()->prepare("SELECT NOW()");
$stmt->execute();

Response::json([
    'current_time' => $stmt->fetchColumn()
]);