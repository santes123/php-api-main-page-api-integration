<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\App\{Bootstrap, Request, Response, Router, ErrorHandler};
use App\App\Middleware\{JsonBodyParser, CorsMiddleware, AuthMiddleware};
use App\Application\Controllers\{AuthController, TaskController, UserController};
use App\Infrastructure\Database;
use App\Config\Config;

ErrorHandler::register();

$bootstrap = new Bootstrap();
$bootstrap->loadEnv(__DIR__ . '/../');

$config = new Config();
$db = new Database($config);

$router = new Router();



//CORS global (antes de router/middlewares)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';             // si usamos cookies, NO usar '*'. Devolver el origen exacto
header("Access-Control-Allow-Origin: $origin");
header("Vary: Origin");
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Responder al preflight y salir
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


// Middlewares globales
$router->use(new CorsMiddleware());
$router->use(new JsonBodyParser());

// Rutas públicas
$router->post('/api/v1/auth/login', [new AuthController($db, $config), 'login']);

// Rutas protegidas
$auth = new AuthMiddleware($config);
$task = new TaskController($db);
$router->get('/api/v1/tasks', [$task, 'index'], [$auth]);
$router->post('/api/v1/tasks', [$task, 'create'], [$auth]);
$router->get('/api/v1/tasks/:id', [$task, 'show'], [$auth]);
$router->put('/api/v1/tasks/:id', [$task, 'update'], [$auth]);
$router->delete('/api/v1/tasks/:id', [$task, 'delete'], [$auth]);

//endpoint para validar el token
$router->get('/api/v1/auth/me', function () {
    http_response_code(204); // token OK, sin body
}, [new AuthMiddleware($config)]);


//listar usuarios (protegido por token)
$user = new UserController($db);
$router->get('/api/v1/users', [$user, 'index'], [$auth]);

//si quisieramos tambien los endpoints para users
/*
$router->get   ('/api/v1/users/:id', [$user,'show'],   [$auth]);
$router->post  ('/api/v1/users',     [$user,'create'], [$auth]);
$router->put   ('/api/v1/users/:id', [$user,'update'], [$auth]);
$router->delete('/api/v1/users/:id', [$user,'delete'], [$auth]);
*/

$request = Request::fromGlobals();
$response = new Response();
$router->dispatch($request, $response);
