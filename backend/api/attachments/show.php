<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

// Extract attachment ID from URL path
$pathInfo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$matches = [];
if (preg_match('/\/api\/attachments\/(\d+)/', $pathInfo, $matches)) {
    $attachmentId = (int) $matches[1];
} else {
    Response::error('Attachment ID not provided', Response::HTTP_BAD_REQUEST);
    exit;
}

// Fetch attachment from database
try {
    $stmt = Database::getPdo()->prepare('
        SELECT filename FROM attachments WHERE id = :id
    ');
    $stmt->execute([':id' => $attachmentId]);
    $attachment = $stmt->fetch();

    if (!$attachment) {
        Response::error('Attachment not found', Response::HTTP_NOT_FOUND);
        exit;
    }

    $filePath = '/mnt/szrotomoto_data/' . $attachment['filename'];

    // Check if file exists
    if (!file_exists($filePath)) {
        Response::error('File not found', Response::HTTP_NOT_FOUND);
        exit;
    }

    // Serve the file
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;

} catch (Exception $e) {
    Response::error('Failed to retrieve attachment', Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}
