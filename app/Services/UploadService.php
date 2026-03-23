<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class UploadService
{
    private const MIME_EXTENSION_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(private readonly array $config = [])
    {
    }

    public function storeSingle(array $file, string $directory): array
    {
        $inspected = $this->inspectUploadedFile($file);
        $targetDirectory = $this->prepareDirectory($directory);
        $filename = bin2hex(random_bytes(20)) . '.' . $inspected['extension'];
        $destination = $targetDirectory . DIRECTORY_SEPARATOR . $filename;

        $moved = is_uploaded_file($file['tmp_name'])
            ? move_uploaded_file($file['tmp_name'], $destination)
            : rename($file['tmp_name'], $destination);

        if (!$moved) {
            throw new RuntimeException('Falha ao salvar o arquivo enviado.');
        }

        chmod($destination, 0644);

        return [
            'file_path' => 'storage/uploads/' . $directory . '/' . $filename,
            'absolute_path' => $destination,
            'original_name' => $file['name'],
            'mime_type' => $inspected['mime_type'],
            'file_size' => (int) filesize($destination),
            'extension' => $inspected['extension'],
        ];
    }

    public function storeMultiple(array $files, string $directory, int $limit): array
    {
        $normalized = $this->normalizeFilesArray($files);
        if (count($normalized) > $limit) {
            throw new RuntimeException('Número máximo de imagens adicionais excedido.');
        }

        $saved = [];
        $errors = [];

        foreach ($normalized as $index => $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            try {
                $saved[] = $this->storeSingle($file, $directory) + ['sort_order' => $index + 1];
            } catch (RuntimeException $exception) {
                $errors[] = sprintf('Imagem adicional %d: %s', $index + 1, $exception->getMessage());
            }
        }

        if ($errors !== []) {
            $this->deleteStoredFiles($saved);
            throw new RuntimeException(implode(' ', $errors));
        }

        return $saved;
    }

    public function deleteStoredFiles(array $storedFiles): void
    {
        foreach ($storedFiles as $file) {
            $absolutePath = $file['absolute_path'] ?? ($file['file_path'] ?? null ? base_path($file['file_path']) : null);
            if ($absolutePath && is_file($absolutePath)) {
                @unlink($absolutePath);
            }
        }
    }

    private function inspectUploadedFile(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->uploadErrorMessage((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
        }

        if (empty($file['tmp_name']) || !is_file($file['tmp_name'])) {
            throw new RuntimeException('Arquivo temporário do upload não foi encontrado.');
        }

        $maxUploadBytes = ((int) ($this->config['max_upload_mb'] ?? 5)) * 1024 * 1024;
        if ((int) $file['size'] > $maxUploadBytes) {
            throw new RuntimeException('Arquivo excede o tamanho máximo permitido.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        $mimeType = is_string($mimeType) ? strtolower($mimeType) : '';
        $allowedMimes = array_map('strtolower', $this->config['allowed_mimes'] ?? []);
        if (!in_array($mimeType, $allowedMimes, true)) {
            throw new RuntimeException('Formato de imagem não permitido.');
        }

        $extension = self::MIME_EXTENSION_MAP[$mimeType] ?? strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array_map('strtolower', $this->config['allowed_extensions'] ?? []);
        if (!in_array($extension, $allowedExtensions, true)) {
            throw new RuntimeException('Extensão de imagem não permitida.');
        }

        if (@getimagesize($file['tmp_name']) === false) {
            throw new RuntimeException('O ficheiro enviado não é uma imagem válida.');
        }

        return [
            'mime_type' => $mimeType,
            'extension' => $extension,
        ];
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

        return array_values(array_filter($normalized, static fn (array $file): bool => ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE));
    }

    private function prepareDirectory(string $directory): string
    {
        $targetDirectory = storage_path('uploads/' . $directory);
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            throw new RuntimeException('Não foi possível preparar o diretório de upload.');
        }

        return $targetDirectory;
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Arquivo excede o tamanho máximo permitido.',
            UPLOAD_ERR_PARTIAL => 'O upload do arquivo foi interrompido antes de concluir.',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário de upload indisponível.',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar o arquivo no servidor.',
            UPLOAD_ERR_EXTENSION => 'O upload foi bloqueado por uma extensão do PHP.',
            default => 'Erro inesperado durante o upload do arquivo.',
        };
    }
}
