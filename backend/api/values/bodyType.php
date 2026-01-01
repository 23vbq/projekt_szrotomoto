<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

Response::json(Consts::getBodyTypes());