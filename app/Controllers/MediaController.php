<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class MediaController extends Controller
{
    public function show(): void
    {
        $path = (string) $this->request->input('path');
        $realBase = realpath(storage_path('uploads'));
        $realFile = realpath(base_path($path));

        if (!$path || !$realBase || !$realFile || !str_starts_with($realFile, $realBase) || !is_file($realFile)) {
            $this->response->setStatus(404);
            echo 'Ficheiro não encontrado.';
            return;
        }

        $mime = mime_content_type($realFile) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string) filesize($realFile));
        readfile($realFile);
        exit;
    }
}
