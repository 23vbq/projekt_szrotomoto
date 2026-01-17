<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : null;

$brandId = isset($_GET['brand_id']) && !empty($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
$modelId = isset($_GET['model_id']) && !empty($_GET['model_id']) ? (int)$_GET['model_id'] : null;
$fuelType = isset($_GET['fuel_type']) && !empty($_GET['fuel_type']) ? trim($_GET['fuel_type']) : null;
$transmission = isset($_GET['transmission']) && !empty($_GET['transmission']) ? trim($_GET['transmission']) : null;
$bodyType = isset($_GET['body_type']) && !empty($_GET['body_type']) ? trim($_GET['body_type']) : null;

$priceMin = isset($_GET['price_min']) && !empty($_GET['price_min']) ? (float)$_GET['price_min'] : null;
$priceMax = isset($_GET['price_max']) && !empty($_GET['price_max']) ? (float)$_GET['price_max'] : null;
$yearMin = isset($_GET['year_min']) && !empty($_GET['year_min']) ? (int)$_GET['year_min'] : null;
$yearMax = isset($_GET['year_max']) && !empty($_GET['year_max']) ? (int)$_GET['year_max'] : null;
$odometerMin = isset($_GET['odometer_min']) && !empty($_GET['odometer_min']) ? (int)$_GET['odometer_min'] : null;
$odometerMax = isset($_GET['odometer_max']) && !empty($_GET['odometer_max']) ? (int)$_GET['odometer_max'] : null;

$sortBy = isset($_GET['sort_by']) && !empty($_GET['sort_by']) ? trim($_GET['sort_by']) : 'created_at';
$sortDir = isset($_GET['sort_dir']) && !empty($_GET['sort_dir']) ? strtoupper(trim($_GET['sort_dir'])) : 'DESC';

$allowedSortFields = ['price', 'odometer', 'production_year', 'created_at'];
$allowedSortDirs = ['ASC', 'DESC'];

if (!in_array($sortBy, $allowedSortFields)) {
    $sortBy = 'created_at';
}
if (!in_array($sortDir, $allowedSortDirs)) {
    $sortDir = 'DESC';
}

$sortField = $sortBy === 'production_year' ? 'o.production_year' : 'o.' . $sortBy;

$qb = (new QueryBuilder(Database::getPdo()))
    ->select('
        o.id, o.created_at, o.updated_at, o.title, o.description, o.price, o.production_year, o.odometer, o.fuel_type, o.transmission, o.displacement, o.horsepower, o.body_type, o.doors_amount, o.seats_amount,
        b.name AS brand_name,
        m.name AS model_name,
        u.name as user_name,
        JSON_UNQUOTE(JSON_EXTRACT(o.attachments, "$[0]")) AS attachment_id
    ')
    ->from('offers', 'o')
    ->innerJoin('models', 'm', 'o.model_id = m.id')
    ->innerJoin('brands', 'b', 'm.brand_id = b.id')
    ->innerJoin('users', 'u', 'o.created_by = u.id')
    ->where('o.status = :status')
    ->setParameter(':status', Consts::OFFER_STATUS_ACTIVE);

if (!empty($search)) {
    $searchTerm = '%' . $search . '%';
    $qb->andWhere('(
        o.title LIKE :search_title 
        OR o.description LIKE :search_description 
        OR b.name LIKE :search_brand 
        OR m.name LIKE :search_model 
        OR o.vin LIKE :search_vin 
        OR o.registration_number LIKE :search_registration
    )')
    ->setParameter(':search_title', $searchTerm)
    ->setParameter(':search_description', $searchTerm)
    ->setParameter(':search_brand', $searchTerm)
    ->setParameter(':search_model', $searchTerm)
    ->setParameter(':search_vin', $searchTerm)
    ->setParameter(':search_registration', $searchTerm);
}

if ($brandId !== null) {
    $qb->andWhere('b.id = :brand_id')
       ->setParameter(':brand_id', $brandId);
}

if ($modelId !== null) {
    $qb->andWhere('m.id = :model_id')
       ->setParameter(':model_id', $modelId);
}

if ($fuelType !== null) {
    $qb->andWhere('o.fuel_type = :fuel_type')
       ->setParameter(':fuel_type', $fuelType);
}

if ($transmission !== null) {
    $qb->andWhere('o.transmission = :transmission')
       ->setParameter(':transmission', $transmission);
}

if ($bodyType !== null) {
    $qb->andWhere('o.body_type = :body_type')
       ->setParameter(':body_type', $bodyType);
}

if ($priceMin !== null) {
    $qb->andWhere('o.price >= :price_min')
       ->setParameter(':price_min', $priceMin);
}
if ($priceMax !== null) {
    $qb->andWhere('o.price <= :price_max')
       ->setParameter(':price_max', $priceMax);
}

if ($yearMin !== null) {
    $qb->andWhere('o.production_year >= :year_min')
       ->setParameter(':year_min', $yearMin);
}
if ($yearMax !== null) {
    $qb->andWhere('o.production_year <= :year_max')
       ->setParameter(':year_max', $yearMax);
}

if ($odometerMin !== null) {
    $qb->andWhere('o.odometer >= :odometer_min')
       ->setParameter(':odometer_min', $odometerMin);
}
if ($odometerMax !== null) {
    $qb->andWhere('o.odometer <= :odometer_max')
       ->setParameter(':odometer_max', $odometerMax);
}

$qb->addOrderBy($sortField, $sortDir);

$offers = $qb->execute()->fetchAll(PDO::FETCH_ASSOC);

Response::json($offers);