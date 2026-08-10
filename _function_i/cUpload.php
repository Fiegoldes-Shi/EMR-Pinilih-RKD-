<?php

/**
 * Memvalidasi ekstensi file yang diunggah terhadap whitelist yang diizinkan.
 * Mengembalikan true jika aman diunggah, false jika ekstensi tidak diizinkan.
 */
function _isAllowedUploadExtension(string $fileName, array $allowedExtensions): bool
{
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    return in_array($ext, $allowedExtensions, true);
}
