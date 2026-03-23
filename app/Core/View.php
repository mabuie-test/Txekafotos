<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function make(string $view, array $data = [], ?string $layout = null): string
    {
        $viewPath = base_path('app/Views/' . $view . '.php');
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found.");
        }

        $payload = array_merge(self::$shared, $data);
        extract($payload, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        $layoutPath = base_path('app/Views/' . $layout . '.php');
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout {$layout} not found.");
        }

        ob_start();
        require $layoutPath;
        return ob_get_clean();
    }
}
