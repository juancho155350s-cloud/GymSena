<?php
namespace GymSena\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $queryParams;
    private array $bodyParams;
    private array $headers;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $parsedUri = parse_url($requestUri, PHP_URL_PATH) ?: '/';
        $this->uri = '/' . trim($parsedUri, '/');
        if ($this->uri !== '/' && substr($this->uri, -1) === '/') {
            $this->uri = rtrim($this->uri, '/');
        }

        $this->queryParams = $_GET;

        // Soporte tanto para formulario POST tradicional como payloads JSON
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $rawInput = file_get_contents('php://input');
            $this->bodyParams = json_decode($rawInput, true) ?: [];
        } else {
            $this->bodyParams = $_POST;
        }

        $this->headers = function_exists('\getallheaders') ? \getallheaders() : [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function get(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->bodyParams[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }

    public function getIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    }

    public function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}

