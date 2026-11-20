<?php
namespace GymSena\Core;

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function view(string $viewName, array $data = [], string $layout = 'main'): void
    {
        // Extraer variables para la vista
        extract($data);

        $viewFile = dirname(__DIR__) . "/Views/{$viewName}.php";
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Error 500: Vista no encontrada: {$viewName}";
            exit;
        }

        // Renderizar vista hija en un buffer
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Renderizar layout
        if ($layout) {
            $layoutFile = dirname(__DIR__) . "/Views/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                include $layoutFile;
                exit;
            }
        }

        echo $content;
        exit;
    }
}

