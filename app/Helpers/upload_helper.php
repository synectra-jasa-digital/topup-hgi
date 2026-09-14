<?php

if (! function_exists('validate_uploaded_image_dimensions')) {
    function validate_uploaded_image_dimensions($file, int $maxWidth = 4096, int $maxHeight = 4096): bool
    {
        if (! $file || ! $file->isValid()) {
            return false;
        }

        $dimensions = @getimagesize($file->getTempName());
        if ($dimensions === false) {
            return false;
        }

        return $dimensions[0] > 0
            && $dimensions[1] > 0
            && $dimensions[0] <= $maxWidth
            && $dimensions[1] <= $maxHeight;
    }
}

if (! function_exists('delete_public_asset')) {
    function delete_public_asset(?string $relativePath): void
    {
        if (! $relativePath || str_starts_with($relativePath, 'http')) {
            return;
        }

        $publicRoot = realpath(FCPATH);
        $assetPath = realpath(FCPATH . ltrim($relativePath, '/\\'));
        if (! $publicRoot || ! $assetPath || ! str_starts_with($assetPath, $publicRoot . DIRECTORY_SEPARATOR)) {
            return;
        }

        if (is_file($assetPath)) {
            @unlink($assetPath);
        }
    }
}
