<?php

namespace App\App\Middleware;

use App\App\{Request, Response};
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\Config;

class AuthMiddleware
{
    public function __construct(private Config $cfg) {}

    public function handle(Request $req, Response $res, callable $next): void
    {
        $auth = $req->headers['Authorization'] ?? '';
        if (!preg_match('/Bearer\s+(.*)/i', $auth, $m)) {
            $res->json(['error' => 'Unauthorized'], 401);
            return;
        }
        try {
            $payload = JWT::decode($m[1], new Key($this->cfg->jwtSecret(), 'HS256'));
            // Adjuntamos al request lo que usaremos en controladores
            $req->params['user_id'] = (int)($payload->sub ?? 0);
            $req->params['role']    = (string)($payload->role ?? 'user');
            $next();
        } catch (\Throwable $e) {
            $res->json(['error' => 'Invalid token'], 401);
        }
    }
}
