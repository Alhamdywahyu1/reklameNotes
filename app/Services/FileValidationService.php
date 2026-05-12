<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileValidationService
{
    // Allowed MIME types untuk setiap dokumen
    const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    // Allowed extensions
    const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];

    // Max file size (5MB)
    const MAX_FILE_SIZE = 5242880; // 5MB in bytes

    /**
     * Validate file upload dengan strict checking
     */
    public static function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // 1. Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            $errors[] = 'Ukuran file maksimal 5MB';
        }

        // 2. Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIMES)) {
            $errors[] = 'Tipe file tidak diizinkan. Gunakan PDF, JPG, atau PNG';
        }

        // 3. Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $errors[] = 'Ekstensi file tidak diizinkan';
        }

        // 4. Verify actual file content (magic bytes)
        if (!self::verifyFileMagicBytes($file)) {
            $errors[] = 'File terdeteksi tidak valid atau rusak';
        }

        return $errors;
    }

    /**
     * Verify file magic bytes untuk prevent file spoofing
     */
    private static function verifyFileMagicBytes(UploadedFile $file): bool
    {
        $path = $file->getRealPath();
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        return in_array($mime, self::ALLOWED_MIMES);
    }

    /**
     * Generate secure filename
     */
    public static function generateSecureFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Remove special characters dan generate random hash
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '', $filename);
        $randomHash = substr(md5(microtime()), 0, 8);
        
        return $filename . '_' . $randomHash . '.' . $extension;
    }
}
