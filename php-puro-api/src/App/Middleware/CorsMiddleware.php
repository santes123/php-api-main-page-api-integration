<?php

namespace App\App\Middleware;

use App\App\{Request, Response};

class CorsMiddleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        header("Vary: Origin");
        header("Access-Control-Allow-Credentials: true"); // opcional

        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
            exit;
        }
        $next();
    }
}
