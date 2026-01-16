<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

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
    ->addOrderBy('created_at', 'DESC')
    ->setParameter(':status', Consts::OFFER_STATUS_ACTIVE);

$offers = $qb->execute()->fetchAll(PDO::FETCH_ASSOC);

Response::json($offers);