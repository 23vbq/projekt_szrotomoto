<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowAuthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$currentUserId = Session::getUserId();

$attachmentIds = [];
if (isset($_FILES['files']) && !empty($_FILES['files']['name'])) {
    $files = $_FILES['files'];
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;
    
    for ($i = 0; $i < $fileCount; $i++) {
        $attachmentId = AttachmentUploader::uploadFile([
            'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
            'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
            'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
            'size' => is_array($files['size']) ? $files['size'][$i] : $files['size']
        ]);
        if ($attachmentId !== null) {
            $attachmentIds[] = $attachmentId;
        }
    }
}

$modelId = isset($_POST['model_id']) ? $_POST['model_id'] : null;
$title = isset($_POST['title']) ? trim($_POST['title']) : null;
$description = isset($_POST['description']) && trim($_POST['description']) !== '' ? trim($_POST['description']) : null;
$price = isset($_POST['price']) ? $_POST['price'] : null;
$productionYear = isset($_POST['production_year']) ? $_POST['production_year'] : null;
$odometer = isset($_POST['odometer']) ? $_POST['odometer'] : null;
$fuelType = isset($_POST['fuel_type']) ? $_POST['fuel_type'] : null;
$transmission = isset($_POST['transmission']) ?  $_POST['transmission'] : null;
$color = isset($_POST['color']) && trim($_POST['color']) !== '' ? trim($_POST['color']) : null;
$displacement = isset($_POST['displacement']) && $_POST['displacement'] !== '' ? $_POST['displacement'] : null;
$horsepower = isset($_POST['horsepower']) && $_POST['horsepower'] !== '' ? $_POST['horsepower'] : null;
$torque = isset($_POST['torque']) && $_POST['torque'] !== '' ? $_POST['torque'] : null;
$bodyType = isset($_POST['body_type']) ? $_POST['body_type'] : null;
$doorsAmount = isset($_POST['doors_amount']) && $_POST['doors_amount'] !== '' ? $_POST['doors_amount'] : null;
$seatsAmount = isset($_POST['seats_amount']) && $_POST['seats_amount'] !== '' ? $_POST['seats_amount'] : null;
$vin = isset($_POST['vin']) ? trim($_POST['vin']) : null;
$registrationNumber = isset($_POST['registration_number']) && trim($_POST['registration_number']) !== '' ? trim($_POST['registration_number']) : null;
$countryOfOrigin = isset($_POST['country_of_origin']) && $_POST['country_of_origin'] !== '' ? $_POST['country_of_origin'] : null;
$isAccidentFree = isset($_POST['is_accident_free']) ? $_POST['is_accident_free'] == '1' : false;
$isFirstHand = isset($_POST['is_first_hand']) ? $_POST['is_first_hand'] == '1' : false;
$isUsed = isset($_POST['is_used']) ? $_POST['is_used'] == '1' : false;
$hasWarranty = isset($_POST['has_warranty']) ? $_POST['has_warranty'] == '1' : false;
$hasServiceBook = isset($_POST['has_service_book']) ? $_POST['has_service_book'] == '1' : false;

if (
    empty($modelId)
    || empty($title)
    || empty($price)
    || empty($productionYear)
    || empty($odometer)
    || empty($fuelType)
    || empty($transmission)
    || empty($bodyType)
    || empty($vin)
) {
    Response::error('Missing required fields', Response::HTTP_BAD_REQUEST);
    exit;
}

$fuelType = in_array($fuelType, Consts::getFuelTypes()) ? $fuelType : null;
$transmission = in_array($transmission, Consts::getTransmissionTypes()) ? $transmission : null;
$bodyType = in_array($bodyType, Consts::getBodyTypes()) ? $bodyType : null;
$countryOfOrigin = in_array($countryOfOrigin, Consts::getCountries()) ? $countryOfOrigin : null;

if ($fuelType === null || $transmission === null || $bodyType === null) {
    Response::error('Invalid request data', Response::HTTP_BAD_REQUEST);
    exit;
}

if ($vin !== null) {
    $stmt = Database::getPdo()->prepare('SELECT id FROM offers WHERE vin = :vin');
    $stmt->execute([':vin' => $vin]);
    if ($stmt->fetch()) {
        Response::error('VIN already exists', Response::HTTP_CONFLICT);
        exit;
    }
}

$stmt = Database::getPdo()->prepare('
    INSERT INTO offers (
        model_id,
        title,
        description,
        price,
        production_year,
        odometer,
        fuel_type,
        transmission,
        color,
        displacement,
        horsepower,
        body_type,
        torque,
        doors_amount,
        seats_amount,
        vin,
        registration_number,
        country_of_origin,
        is_accident_free,
        is_first_hand,
        is_used,
        has_warranty,
        has_service_book,
        created_by,
        attachments)
    VALUES (
        :model_id,
        :title,
        :description,
        :price,
        :production_year,
        :odometer,
        :fuel_type,
        :transmission,
        :color,
        :displacement,
        :horsepower,
        :body_type,
        :torque,
        :doors_amount,
        :seats_amount,
        :vin,
        :registration_number,
        :country_of_origin,
        :is_accident_free,
        :is_first_hand,
        :is_used,
        :has_warranty,
        :has_service_book,
        :created_by,
        :attachments
    )
');
$result = $stmt->execute([
    ':model_id' => $modelId,
    ':title' => $title,
    ':description' => $description,
    ':price' => $price,
    ':production_year' => $productionYear,
    ':odometer' => $odometer,
    ':fuel_type' => $fuelType,
    ':transmission' => $transmission,
    ':color' => $color,
    ':displacement' => $displacement,
    ':horsepower' => $horsepower,
    ':body_type' => $bodyType,
    ':torque' => $torque,
    ':doors_amount' => $doorsAmount,
    ':seats_amount' => $seatsAmount,
    ':vin' => $vin,
    ':registration_number' => $registrationNumber,
    ':country_of_origin' => $countryOfOrigin,
    ':is_accident_free' => $isAccidentFree ? 1 : 0,
    ':is_first_hand' => $isFirstHand ? 1 : 0,
    ':is_used' => $isUsed ? 1 : 0,
    ':has_warranty' => $hasWarranty ? 1 : 0,
    ':has_service_book' => $hasServiceBook ? 1 : 0,
    ':created_by' => $currentUserId,
    ':attachments' => !empty($attachmentIds) ? json_encode($attachmentIds) : null
]);
if (!$result) {
    Response::error('Failed to create offer', Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}
$newOfferId = Database::getPdo()->lastInsertId();

Response::json([
    'offer_id' => $newOfferId,
    'message' => 'Offer created successfully'
], Response::HTTP_CREATED);