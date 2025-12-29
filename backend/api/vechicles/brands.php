<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowAuthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$db = new Database();
$brandsStmt = $db->getPdo()->prepare('SELECT id, name FROM brands_asd ORDER BY name ASC');
$brandsStmt->execute();
$brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($brands);