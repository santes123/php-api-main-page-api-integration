<?php

namespace App\Config;

class Config
{
    public function env(string $k, ?string $d = null): ?string
    {
        $v = getenv($k);
        return $v === false ? $d : $v;
    }
    public function dsn(): string
    {
        return sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $this->env('DB_HOST', '127.0.0.1'), $this->env('DB_PORT', '3306'), $this->env('DB_NAME', 'gestor_tareas'));
    }
    public function user(): string
    {
        return $this->env('DB_USER', 'root') ?? 'root';
    }
    public function pass(): string
    {
        return $this->env('DB_PASSWORD', '') ?? '';
    }
    public function jwtSecret(): string
    {
        return $this->env('JWT_SECRET', 'xg4Kq9hDHxt4dILYP4ZKNxTXHKUAbm') ?? 'xg4Kq9hDHxt4dILYP4ZKNxTXHKUAbm';
    }
}
