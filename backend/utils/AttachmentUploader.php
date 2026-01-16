<?php

class AttachmentUploader {
    const UPLOAD_DIR = '/mnt/szrotomoto_data';
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    const ALLOWED_MIMETYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    /**
     * Upload a single file and create attachment record
     * 
     * @param array $file Single file from $_FILES['file'] or $_FILES['files'][i]
     * @return int|null Attachment ID or null if upload failed
     */
    public static function uploadFile($file): ?int {
        if (!isset($file['name']) || empty($file['name'])) {
            return null;
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];

        if ($fileError !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($fileSize > self::MAX_FILE_SIZE) {
            return null;
        }

        $mimeType = mime_content_type($fileTmpName);
        
        if (!in_array($mimeType, self::ALLOWED_MIMETYPES)) {
            return null;
        }

        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid('attachment_') . '.' . $fileExtension;
        $uploadPath = self::UPLOAD_DIR . '/' . $uniqueFileName;

        if (!move_uploaded_file($fileTmpName, $uploadPath)) {
            return null;
        }

        try {
            $stmt = Database::getPdo()->prepare('
                INSERT INTO attachments (filename)
                VALUES (:filename)
            ');
            $result = $stmt->execute([
                ':filename' => $uniqueFileName
            ]);

            if ($result) {
                return (int) Database::getPdo()->lastInsertId();
            }
        } catch (Exception $e) {
            @unlink($uploadPath);
        }

        return null;
    }
}



