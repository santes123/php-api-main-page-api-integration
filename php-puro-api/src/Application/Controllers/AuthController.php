<?php

namespace App\Application\Controllers;

use App\App\{Request, Response};
use App\Infrastructure\Database;
use App\Application\Services\AuthService;
use App\Config\Config;

class AuthController
{
    private AuthService $svc;
    public function __construct(Database $db, Config $cfg)
    {
        $this->svc = new AuthService($db, $cfg);
    }

    public function login(Request $req, Response $res): void
    {
        $email = $req->body['email'] ?? '';
        $pass  = $req->body['password'] ?? '';
        if (!$email || !$pass) {
            $res->json(['error' => 'Email and password required'], 422);
            return;
        }

        $auth = $this->svc->attempt($email, $pass); //devolvemos token e info de usuario
        if (!$auth) {
            $res->json(['error' => 'Invalid credentials'], 401);
            return;
        }
        $res->json([
            'token' => $auth['token'],
            'user'  => [
                'role'       => $auth['role'],
                'is_admin'   => $auth['is_admin']
            ]
        ]);
    }
}
