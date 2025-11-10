<?php

namespace App\App\Middleware;

use App\App\{Request, Response};

class JsonBodyParser
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        $ct = $req->headers['Content-Type'] ?? $req->headers['content-type'] ?? '';
        if (str_contains($ct, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) $req->body = $data;
        }
        $next();
    }
}
