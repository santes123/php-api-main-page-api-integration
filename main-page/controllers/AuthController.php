<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../core/response.php';

class AuthController
{
    public static function showLogin()
    {
        require __DIR__ . '/../views/auth/login.php';
    }
    public static function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $u = UserModel::findByEmail($email);
        if (!$u || !password_verify($password, $u['password_hash'])) json_error('Credenciales inválidas', 401);
        login_user(['id' => $u['id'], 'email' => $u['email'], 'first_name' => $u['first_name'], 'last_name' => $u['last_name'], 'role' => $u['role']]);
        json_ok(['ok' => true]);
    }
    public static function logout()
    {
        logout_user();
        header('Location: /?r=auth/login');
    }
}
