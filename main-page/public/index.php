<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set('display_errors', '0');

require_once __DIR__ . '/../config/session.php';

$r = $_GET['r'] ?? 'tasks/index'; // ruta por defecto tras login
[$ctrl, $act] = array_pad(explode('/', $r, 2), 2, 'index');

$map = [
    'auth'  => 'AuthController',
    'users' => 'UsersController',
    'tasks' => 'TasksController',
];

if (!isset($map[$ctrl])) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

require_once __DIR__ . "/../controllers/{$map[$ctrl]}.php";
$class = $map[$ctrl];

if (!method_exists($class, $act)) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

call_user_func([$class, $act]);
