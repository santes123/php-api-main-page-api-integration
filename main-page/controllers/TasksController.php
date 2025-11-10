<?php
require_once __DIR__ . '/../models/TaskModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../core/guard.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../core/response.php';

class TasksController
{
    public static function show()
    {
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(404);
            echo 'No encontrada';
            return;
        }

        $task = TaskModel::find($id);
        if (!$task) {
            http_response_code(404);
            echo 'No encontrada';
            return;
        }
        if (!is_admin() && $task['user_id'] != current_user()['id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $pdo = db();
        $u = $pdo->prepare('SELECT email, first_name, last_name FROM users WHERE id=?');
        $u->execute([$task['user_id']]);
        $user = $u->fetch() ?: ['email' => '(desconocido)', 'first_name' => '', 'last_name' => ''];

        $pageTitle = 'Tarea #' . $task['id'];
        require __DIR__ . '/../views/tasks/show.php';
    }
    public static function index()
    {
        require_login();
        $filters = [];
        if (!is_admin()) $filters['user_id'] = current_user()['id']; // usuarios normales: sólo sus tareas
        $completedParam = $_GET['completed'] ?? null;
        if ($completedParam !== null && $completedParam !== '') {
            // Solo aplico el filtro si viene 0 o 1 explícito, para evitar problemas
            $filters['completed'] = $completedParam === '1' ? 1 : 0;
        }

        if (!empty($_GET['q'])) $filters['search'] = $_GET['q'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $tasks = TaskModel::all($filters, $limit, $offset);
        $total = TaskModel::count($filters);
        $users = is_admin() ? UserModel::all(null, 1000, 0) : []; // para selector de admin en tasks.php
        require __DIR__ . '/../views/tasks/index.php';
    }
    public static function store()
    {
        require_login();
        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $data = [
            'user_id'    => is_admin() ? (int)$_POST['user_id'] : current_user()['id'],
            'title'      => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'starts_at'  => $_POST['starts_at'] ?? null,
            'ends_at'    => $_POST['ends_at'] ?? null,
            'completed'  => (int)($_POST['completed'] ?? 0),
        ];
        if (!$data['title']) json_error('Título requerido');
        if ($data['starts_at'] && $data['ends_at'] && ($data['ends_at'] < $data['starts_at'])) json_error('La fecha fin no puede ser anterior al inicio');
        $id = TaskModel::create($data);
        json_ok(['id' => $id], 201);
    }
    public static function update()
    {
        require_login();
        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) json_error('ID requerido');
        $task = TaskModel::find($id);
        if (!$task) json_error('No encontrada', 404);
        if (!is_admin() && $task['user_id'] != current_user()['id']) json_error('Forbidden', 403);
        $data = [
            'user_id'    => is_admin() ? (int)$_POST['user_id'] : $task['user_id'],
            'title'      => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'starts_at'  => $_POST['starts_at'] ?? null,
            'ends_at'    => $_POST['ends_at'] ?? null,
            'completed'  => (int)($_POST['completed'] ?? 0),
        ];
        if (!$data['title']) json_error('Título requerido');
        if ($data['starts_at'] && $data['ends_at'] && ($data['ends_at'] < $data['starts_at'])) json_error('La fecha fin no puede ser anterior al inicio');
        TaskModel::update($id, $data);
        json_ok(['ok' => true]);
    }
    public static function destroy()
    {
        require_login();
        if (!csrf_check($_POST['csrf'] ?? '')) json_error('CSRF', 419);
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) json_error('ID requerido');
        $task = TaskModel::find($id);
        if (!$task) json_error('No encontrada', 404);
        if (!is_admin() && $task['user_id'] != current_user()['id']) json_error('Forbidden', 403);
        TaskModel::delete($id);
        json_ok(['ok' => true]);
    }
}
