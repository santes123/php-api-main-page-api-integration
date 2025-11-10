<?php
function current_user()
{
    return $_SESSION['user'] ?? null;
}
function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}
function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}
function login_user(array $user)
{
    $_SESSION['user'] = $user;
}
function logout_user()
{
    $_SESSION = [];
    session_destroy();
}
