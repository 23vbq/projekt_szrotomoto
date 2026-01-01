<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$brandId = isset($_GET['brand_id']) ? $_GET['brand_id'] : null;

if ($brandId !== null && !is_numeric($brandId)) {
    Response::error('Invalid brand_id parameter', Response::HTTP_BAD_REQUEST);
    exit;
}

$qb = (new QueryBuilder(Database::getPdo()))
    ->select('id, name, brand_id')
    ->from('models')
    ->addOrderBy('name', 'ASC');

if (!empty($brandId)) {
    $qb->where('brand_id = :brand_id')
       ->setParameter(':brand_id', $brandId);
}

$models = $qb->execute()->fetchAll(PDO::FETCH_ASSOC);

Response::json($models);