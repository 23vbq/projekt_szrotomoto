<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$brandId = isset($_GET['brand_id']) ? $_GET['brand_id'] : null;

$db = new Database();
$modelsStmt = $db->getPdo()->prepare('SELECT
    id, name, brand_id
FROM models
WHERE brand_id = :brand_id
ORDER BY name ASC');
$modelsStmt->execute([
    'brand_id' => $brandId
]);
$models = $modelsStmt->fetchAll(PDO::FETCH_ASSOC); 

Response::json($models);