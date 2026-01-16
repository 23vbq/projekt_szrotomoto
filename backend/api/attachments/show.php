<?php
require_once __DIR__ . '/../../utils_loader.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

// Get attachment ID from query parameter
$attachmentId = isset($_GET['id']) ? (int) $_GET['id'] : null;
if (empty($attachmentId)) {
    Response::error('Attachment ID not provided', Response::HTTP_BAD_REQUEST);
    exit;
}

// Fetch attachment from database
try {
    $stmt = Database::getPdo()->prepare('
        SELECT filename, mime_type FROM attachments WHERE id = :id
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

    // Serve the file as inline image with stored MIME type
    header('Content-Type: ' . $attachment['mime_type']);
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;

} catch (Exception $e) {
    Response::error('Failed to retrieve attachment', Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}
