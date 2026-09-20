<?php

if (! function_exists('banner_image_set')) {
    /**
     * Public URLs for a banner image: the original, and, when resized WebP copies exist next to a PNG,
     * the WebP and a ready-made srcset (400w / 700w when present, then the full 900w).
     *
     * @return array{src: string, webp: ?string, srcset: ?string}
     */
    function banner_image_set(string $imagePath): array
    {
        $isLocal = ! str_starts_with($imagePath, 'http');
        $src     = $isLocal ? base_url($imagePath) : $imagePath;
        $plain   = ['src' => $src, 'webp' => null, 'srcset' => null];

        if (! $isLocal || ! str_ends_with($imagePath, '.png')) {
            return $plain;
        }

        $webpPath = substr($imagePath, 0, -4) . '.webp';
        if (! is_file(FCPATH . $webpPath)) {
            return $plain;
        }

        $tiers = [];
        foreach ([400, 700] as $width) {
            $resized = substr($webpPath, 0, -5) . "-{$width}w.webp";
            if (is_file(FCPATH . $resized)) {
                $tiers[] = base_url($resized) . " {$width}w";
            }
        }
        $tiers[] = base_url($webpPath) . ' 900w';

        return ['src' => $src, 'webp' => base_url($webpPath), 'srcset' => implode(', ', $tiers)];
    }
}
