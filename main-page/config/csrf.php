<?php
require_once __DIR__ . '/env.php';
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16)) . env('APP_KEY', '');
    }
    return hash('sha256', $_SESSION['csrf']);
}
function csrf_check($token): bool
{
    return hash_equals(csrf_token(), $token ?? '');
}
