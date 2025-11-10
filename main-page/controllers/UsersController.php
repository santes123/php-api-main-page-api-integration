<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/TaskModel.php';
require_once __DIR__ . '/../core/guard.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../core/response.php';

class UsersController
{
    public static function show()
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(404);
            echo 'No encontrado';
            return;
        }

        $user = UserModel::find($id);
        if (!$user) {
            http_response_code(404);
            echo 'No encontrado';
            return;
        }

        // pequeña lista de tareas del usuario
        //$tasks = TaskModel::all(['id' => $id], 10, 0); 
        $tasks = TaskModel::all(['user_id' => $id], 10, 0); //corregido
        $pageTitle = 'Usuario #' . $user['id'];
        require __DIR__ . '/../views/users/show.php';
    }
    public static function index()
    {
        require_admin();
        $search = $_GET['q'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $users = UserModel::all($search, $limit, $offset);
        $total = UserModel::count($search);
        require __DIR__ . '/../views/users/index.php';
    }
    public static function store()
    {
        require_admin();
        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $email = trim($_POST['email'] ?? '');
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $role  = $_POST['role'] ?? 'admin';
        $pwd   = $_POST['password'] ?? '';
        if (!$email || !$first || !$last || !$pwd) json_error('Campos requeridos');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('Email inválido');
        $id = UserModel::create([
            'email' => $email,
            'password_hash' => password_hash($pwd, PASSWORD_BCRYPT),
            'first_name' => $first,
            'last_name' => $last,
            'role' => $role
        ]);
        json_ok(['id' => $id], 201);
    }
    public static function update()
    {
        require_admin();
        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $id = (int)($_POST['id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $role  = $_POST['role'] ?? 'admin';
        $pwd   = $_POST['password'] ?? '';
        if (!$id || !$email || !$first || !$last) json_error('Campos requeridos');
        $payload = ['email' => $email, 'first_name' => $first, 'last_name' => $last, 'role' => $role];
        if ($pwd) $payload['password_hash'] = password_hash($pwd, PASSWORD_BCRYPT);
        UserModel::update($id, $payload);
        json_ok(['ok' => true]);
    }
    public static function destroy()
    {
        require_admin();
        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) json_error('ID requerido');
        UserModel::delete($id);
        json_ok(['ok' => true]);
    }
}
