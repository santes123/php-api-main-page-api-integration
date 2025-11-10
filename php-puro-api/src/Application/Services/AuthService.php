<?php

namespace App\Application\Services;

use App\Infrastructure\Database;
use Firebase\JWT\JWT;
use App\Config\Config;


class AuthService
{
    public function __construct(private Database $db, private Config $config) {}

    //verificar conexion del usuario y devolver un token adecuado + role
    public function attempt(string $email, string $password): ?array
    {
        $st = $this->db->pdo->prepare(
            'SELECT id, password_hash, role, first_name, last_name
             FROM users WHERE email = :email'
        );
        $st->execute([':email' => $email]);
        $u = $st->fetch();
        if (!$u || !password_verify($password, $u['password_hash'])) return null;

        $payload = [
            'iss' => 'php-puro-api',
            'sub' => (int)$u['id'],
            'role' => (string)$u['role'],
            'fn'  => (string)$u['first_name'],
            'ln'  => (string)$u['last_name'],
            'iat' => time(),
            'exp' => time() + 3600 * 8
        ];
        $token = JWT::encode($payload, $this->config->jwtSecret(), 'HS256');

        return [
            'token'    => $token,
            'role'     => (string)$u['role'],
            'is_admin' => ((string)$u['role'] === 'admin'),
        ];
    }
}
