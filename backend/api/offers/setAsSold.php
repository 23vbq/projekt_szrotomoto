<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowAuthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$offerId = isset($_GET['offer_id']) ? $_GET['offer_id'] : null;
if (empty($offerId) || !is_numeric($offerId)) {
    Response::error('Invalid or missing offer_id parameter', Response::HTTP_BAD_REQUEST);
    exit;
}

$stmt = Database::getPdo()->prepare('SELECT o.created_by FROM offers o WHERE o.id = :offer_id');
$stmt->bindParam(':offer_id', $offerId, PDO::PARAM_INT);
$stmt->execute();
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    Response::error('Offer not found', Response::HTTP_NOT_FOUND);
    exit;
}

$currentUserId = Session::getUserId();
if ($offer['created_by'] != $currentUserId) {
    Response::error('Unauthorized to set this offer as sold', Response::HTTP_FORBIDDEN);
    exit;
}

$stmt = Database::getPdo()->prepare('UPDATE offers SET status = :status, updated_at = NOW() WHERE id = :offer_id');
$result = $stmt->execute([
    ':status' => Consts::OFFER_STATUS_SOLD,
    ':offer_id' => $offerId
]);

if (!$result) {
    Response::error('Failed to set offer as sold', Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}

$stmt = Database::getPdo()->prepare('
    SELECT
        o.*, 
        b.name AS brand_name,
        m.name AS model_name,
        u.name AS user_name
    FROM offers o
    INNER JOIN models m ON o.model_id = m.id
    INNER JOIN brands b ON m.brand_id = b.id
    INNER JOIN users u ON o.created_by = u.id
    WHERE o.id = :offer_id
');
$stmt->bindParam(':offer_id', $offerId, PDO::PARAM_INT);
$stmt->execute();
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

Response::json($offer);