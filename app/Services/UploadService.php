<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class UploadService
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function storeSingle(array $file, string $directory): array
    {
        $this->guardUploadedFile($file);
        $targetDirectory = storage_path('uploads/' . $directory);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0775, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(20)) . '.' . $extension;
        $destination = $targetDirectory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Falha ao salvar o arquivo enviado.');
        }

        return [
            'file_path' => 'storage/uploads/' . $directory . '/' . $filename,
            'original_name' => $file['name'],
            'mime_type' => mime_content_type($destination) ?: $file['type'],
            'file_size' => filesize($destination) ?: (int) $file['size'],
        ];
    }

    public function storeMultiple(array $files, string $directory, int $limit): array
    {
        $normalized = $this->normalizeFilesArray($files);
        if (count($normalized) > $limit) {
            throw new RuntimeException('Número máximo de imagens adicionais excedido.');
        }

        $saved = [];
        foreach ($normalized as $index => $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $saved[] = $this->storeSingle($file, $directory) + ['sort_order' => $index + 1];
        }

        return $saved;
    }

    public function storeFromPath(string $sourcePath, string $directory): string
    {
        $targetDirectory = storage_path('uploads/' . $directory);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0775, true);
        }
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = bin2hex(random_bytes(20)) . '.' . strtolower($extension);
        $destination = $targetDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!copy($sourcePath, $destination)) {
            throw new RuntimeException('Não foi possível copiar o arquivo final.');
        }
        return 'storage/uploads/' . $directory . '/' . $filename;
    }

    private function guardUploadedFile(array $file): void
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Erro no upload do arquivo.');
        }

        $maxUploadBytes = ((int) ($this->config['max_upload_mb'] ?? 5)) * 1024 * 1024;
        if ((int) $file['size'] > $maxUploadBytes) {
            throw new RuntimeException('Arquivo excede o tamanho máximo permitido.');
        }

        $mime = mime_content_type($file['tmp_name']) ?: '';
        $allowedMimes = $this->config['allowed_mimes'] ?? [];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = $this->config['allowed_extensions'] ?? [];

        if (!in_array($mime, $allowedMimes, true) || !in_array($extension, $allowedExtensions, true)) {
            throw new RuntimeException('Formato de imagem não permitido.');
        }
    }

    private function normalizeFilesArray(array $files): array
    {
        $normalized = [];
        foreach ($files['name'] ?? [] as $index => $name) {
            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }
        return $normalized;
    }
}
