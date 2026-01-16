<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowAuthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['file'])) {
    Response::error('No file provided', Response::HTTP_BAD_REQUEST);
    exit;
}

// Upload file
$attachmentId = AttachmentUploader::uploadFile($_FILES['file']);

if ($attachmentId === null) {
    Response::error('Failed to upload file', Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}

Response::json([
    'attachment_id' => $attachmentId,
    'message' => 'File uploaded successfully'
], Response::HTTP_CREATED);

