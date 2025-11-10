<?php

namespace App\App;

class Request
{
    public string $method;
    public string $uri;
    public array $headers;
    public array $query;
    public array $body = [];
    public array $params = [];

    public static function fromGlobals(): self
    {
        $r = new self();
        $r->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $r->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $r->headers = function_exists('getallheaders') ? getallheaders() : [];
        $r->query = $_GET ?? [];
        return $r;
    }
}
