<?php
require_once __DIR__ . '/../utils_loader.php';

$db = new Database();
$pdo = $db->getPdo();

print "Seeding database...".PHP_EOL;
$startTime = microtime(true);

// Get data from vehicles.json
$vechicles = file_get_contents(__DIR__ . '/vechicles.json');
$vechicles = json_decode($vechicles, true);
$brands = array_keys($vechicles);

// Insert brands into the database
$sql = 'INSERT INTO brands (name) VALUES ';
foreach ($brands as $brand) {
    $sql .= '(?),';
}
$insertStmt = $pdo->prepare(rtrim($sql, ','));
$insertStmt->execute($brands);

// Get ids of inserted brands
$brandIdStmt = $pdo->prepare('SELECT id, name FROM brands');
$brandIdStmt->execute();
$brandIds = $brandIdStmt->fetchAll(PDO::FETCH_ASSOC);
$brandIds = ArrayUtils::mapByColumn($brandIds, 'name');

// Insert models into the database
$sql = 'INSERT INTO models (brand_id, name) VALUES ';
$params = [];
foreach ($vechicles as $brand => $models) {
    $brandId = $brandIds[$brand]['id'];
    foreach ($models as $model) {
        $sql .= '(?, ?),';
        $params[] = $brandId;
        $params[] = $model;
    }
}
$insertStmt = $pdo->prepare(rtrim($sql, ','));
$insertStmt->execute($params);

$endTime = microtime(true);
$duration = $endTime - $startTime;
print "Seeding completed in " . number_format($duration, 2) . " seconds.".PHP_EOL;