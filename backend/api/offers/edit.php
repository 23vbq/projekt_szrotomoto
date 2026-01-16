<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowAuthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$offerId = isset($_GET['offer_id']) ? $_GET['offer_id'] : null;
if (empty($offerId) || !is_numeric($offerId)) {
    Response::error('Invalid or missing offer_id parameter', Response::HTTP_BAD_REQUEST);
    exit;
}

$stmt = Database::getPdo()->prepare('SELECT * FROM offers WHERE id = :offer_id');
$stmt->bindParam(':offer_id', $offerId, PDO::PARAM_INT);
$stmt->execute();
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    Response::error('Offer not found', Response::HTTP_NOT_FOUND);
    exit;
}

$currentUserId = Session::getUserId();
if ($offer['created_by'] != $currentUserId) {
    Response::error('Unauthorized to edit this offer', Response::HTTP_FORBIDDEN);
    exit;
}

$edit = [];
$edit['title'] = isset($_POST['title']) && !empty($_POST['title']) ? trim($_POST['title']) : null;
$edit['description'] = isset($_POST['description']) && !empty($_POST['description']) ? trim($_POST['description']) : null;
$edit['price'] = isset($_POST['price']) && !empty($_POST['price']) ? $_POST['price'] : null;
$edit['productionYear'] = isset($_POST['production_year']) && !empty($_POST['production_year']) ? $_POST['production_year'] : null;
$edit['odometer'] = isset($_POST['odometer']) && !empty($_POST['odometer']) ? $_POST['odometer'] : null;
$edit['fuelType'] = isset($_POST['fuel_type']) && !empty($_POST['fuel_type']) ? $_POST['fuel_type'] : null;
$edit['transmission'] = isset($_POST['transmission']) && !empty($_POST['transmission']) ?  $_POST['transmission'] : null;
$edit['color'] = isset($_POST['color']) && !empty($_POST['color']) ? trim($_POST['color']) : null;
$edit['displacement'] = isset($_POST['displacement']) && !empty($_POST['displacement']) ? $_POST['displacement'] : null;
$edit['horsepower'] = isset($_POST['horsepower']) && !empty($_POST['horsepower']) ? $_POST['horsepower'] : null;
$edit['torque'] = isset($_POST['torque']) && !empty($_POST['torque']) ? $_POST['torque'] : null;
$edit['bodyType'] = isset($_POST['body_type']) && !empty($_POST['body_type']) ? $_POST['body_type'] : null;
$edit['doorsAmount'] = isset($_POST['doors_amount']) && !empty($_POST['doors_amount']) ? $_POST['doors_amount'] : null;
$edit['seatsAmount'] = isset($_POST['seats_amount']) && !empty($_POST['seats_amount']) ? $_POST['seats_amount'] : null;
$edit['vin'] = isset($_POST['vin']) && !empty($_POST['vin']) ? trim($_POST['vin']) : null;
$edit['registrationNumber'] = isset($_POST['registration_number']) && !empty($_POST['registration_number']) ? trim($_POST['registration_number']) : null;
$edit['countryOfOrigin'] = isset($_POST['country_of_origin']) && !empty($_POST['country_of_origin']) ? $_POST['country_of_origin'] : null;
$edit['isAccidentFree'] = isset($_POST['is_accident_free']) && !empty($_POST['is_accident_free']) ? $_POST['is_accident_free'] == '1' : null;
$edit['isFirstHand'] = isset($_POST['is_first_hand']) && !empty($_POST['is_first_hand']) ? $_POST['is_first_hand'] == '1' : null;
$edit['isUsed'] = isset($_POST['is_used']) && !empty($_POST['is_used']) ? $_POST['is_used'] == '1' : null;
$edit['hasWarranty'] = isset($_POST['has_warranty']) && !empty($_POST['has_warranty']) ? $_POST['has_warranty'] == '1' : null;
$edit['hasServiceBook'] = isset($_POST['has_service_book']) && !empty($_POST['has_service_book']) ? $_POST['has_service_book'] == '1' : null;

if ($edit['fuelType'] !== null) {
    $fuelType = in_array($edit['fuelType'], Consts::getFuelTypes()) ? $edit['fuelType'] : false;
}
if ($edit['transmission'] !== null) {
    $transmission = in_array($edit['transmission'], Consts::getTransmissionTypes()) ? $edit['transmission'] : false;
}
if ($edit['bodyType'] !== null) {
    $bodyType = in_array($edit['bodyType'], Consts::getBodyTypes()) ? $edit['bodyType'] : false;
}
if ($edit['countryOfOrigin'] !== null) {
    $countryOfOrigin = in_array($edit['countryOfOrigin'], Consts::getCountries()) ? $edit['countryOfOrigin'] : false;
}

if ($edit['fuelType'] === false || $edit['transmission'] === false || $edit['bodyType'] === false || $edit['countryOfOrigin'] === false) {
    Response::error('Invalid request data', Response::HTTP_BAD_REQUEST);
    exit;
}

$offer['title'] = $edit['title'] ?? $offer['title'];
$offer['description'] = $edit['description'] ?? $offer['description'];
$offer['price'] = $edit['price'] ?? $offer['price'];
$offer['production_year'] = $edit['productionYear'] ?? $offer['production_year'];
$offer['odometer'] = $edit['odometer'] ?? $offer['odometer'];
$offer['fuel_type'] = $edit['fuelType'] ?? $offer['fuel_type'];
$offer['transmission'] = $edit['transmission'] ?? $offer['transmission'];
$offer['color'] = $edit['color'] ?? $offer['color'];
$offer['displacement'] = $edit['displacement'] ?? $offer['displacement'];
$offer['horsepower'] = $edit['horsepower'] ?? $offer['horsepower'];
$offer['torque'] = $edit['torque'] ?? $offer['torque'];
$offer['body_type'] = $edit['bodyType'] ?? $offer['body_type'];
$offer['doors_amount'] = $edit['doorsAmount'] ?? $offer['doors_amount'];
$offer['seats_amount'] = $edit['seatsAmount'] ?? $offer['seats_amount'];
$offer['vin'] = $edit['vin'] ?? $offer['vin'];
$offer['registration_number'] = $edit['registrationNumber'] ?? $offer['registration_number'];
$offer['country_of_origin'] = $edit['countryOfOrigin'] ?? $offer['country_of_origin'];
$offer['is_accident_free'] = $edit['isAccidentFree'] ?? $offer['is_accident_free'];
$offer['is_first_hand'] = $edit['isFirstHand'] ?? $offer['is_first_hand'];
$offer['is_used'] = $edit['isUsed'] ?? $offer['is_used'];
$offer['has_warranty'] = $edit['hasWarranty'] ?? $offer['has_warranty'];
$offer['has_service_book'] = $edit['hasServiceBook'] ?? $offer['has_service_book'];

$stmt = Database::getPdo()->prepare('
    UPDATE offers SET
        title = :title,
        description = :description,
        price = :price,
        production_year = :production_year,
        odometer = :odometer,
        fuel_type = :fuel_type,
        transmission = :transmission,
        color = :color,
        displacement = :displacement,
        horsepower = :horsepower,
        torque = :torque,
        body_type = :body_type,
        doors_amount = :doors_amount,
        seats_amount = :seats_amount,
        vin = :vin,
        registration_number = :registration_number,
        country_of_origin = :country_of_origin,
        is_accident_free = :is_accident_free,
        is_first_hand = :is_first_hand,
        is_used = :is_used,
        has_warranty = :has_warranty,
        has_service_book = :has_service_book,
        updated_at = NOW()
    WHERE id = :offer_id
');
$result = $stmt->execute([
    ':title' => $offer['title'],
    ':description' => $offer['description'],
    ':price' => $offer['price'],
    ':production_year' => $offer['production_year'],
    ':odometer' => $offer['odometer'],
    ':fuel_type' => $offer['fuel_type'],
    ':transmission' => $offer['transmission'],
    ':color' => $offer['color'],
    ':displacement' => $offer['displacement'],
    ':horsepower' => $offer['horsepower'],
    ':torque' => $offer['torque'],
    ':body_type' => $offer['body_type'],
    ':doors_amount' => $offer['doors_amount'],
    ':seats_amount' => $offer['seats_amount'],
    ':vin' => $offer['vin'],
    ':registration_number' => $offer['registration_number'],
    ':country_of_origin' => $offer['country_of_origin'],
    ':is_accident_free' => $offer['is_accident_free'],
    ':is_first_hand' => $offer['is_first_hand'],
    ':is_used' => $offer['is_used'],
    ':has_warranty' => $offer['has_warranty'],
    ':has_service_book' => $offer['has_service_book'],
    ':offer_id' => $offerId
]);

if (!$result) {
    Response::error('Failed to update offer', Response::HTTP_INTERNAL_SERVER_ERROR);
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

Response::json($stmt->fetch(PDO::FETCH_ASSOC));