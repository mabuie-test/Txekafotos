<?php

declare(strict_types=1);

return [
    'upload' => [
        'max_upload_mb' => (int) env('MAX_UPLOAD_MB', 5),
        'max_extra_images' => (int) env('MAX_EXTRA_IMAGES', 5),
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
    ],
    'orders' => [
        'max_revisions' => (int) env('MAX_REVISIONS', 2),
    ],
];
