<?php

namespace App\Application\Controllers;

use App\App\{Request, Response};
use App\Infrastructure\Database;
use App\Infrastructure\Repositories\UserRepository;

class UserController
{
    private UserRepository $repo;
    public function __construct(Database $db)
    {
        $this->repo = new UserRepository($db);
    }

    //verificar si current user es admin o no
    private function ensureAdmin(Request $req, Response $res): bool
    {
        if (($req->params['role'] ?? 'user') !== 'admin') {
            $res->json(['error' => 'Forbidden'], 403);
            return false;
        }
        return true;
    }

    public function index(Request $req, Response $res): void
    {
        //if (!$this->ensureAdmin($req, $res)) return; //usando la validación de admin, solo admin podria ver los usuarios
        $page = max(1, (int)($req->query['page'] ?? 1));
        $per = min(100, max(1, (int)($req->query['per_page'] ?? 20)));
        $data = $this->repo->list($page, $per);
        $total = $this->repo->count();
        $res->json(['data' => $data, 'page' => $page, 'per_page' => $per, 'total' => $total]);
    }
    //endpoints CRUD de usuarios si se desearan
    /*
    public function show(Request $req, Response $res): void
    {
        if (!$this->ensureAdmin($req, $res)) return;
        $id = (int)($req->params['id'] ?? 0);
        $u = $this->repo->find($id);
        if (!$u) {
            $res->json(['error' => 'Not Found'], 404);
            return;
        }
        $res->json(['data' => $u]);
    }

    public function create(Request $req, Response $res): void
    {
        if (!$this->ensureAdmin($req, $res)) return;
        $email = trim((string)($req->body['email'] ?? ''));
        $pass  = (string)($req->body['password'] ?? '');
        $first = trim((string)($req->body['first_name'] ?? ''));
        $last  = trim((string)($req->body['last_name'] ?? ''));
        $role  = (string)($req->body['role'] ?? 'user');
        if ($email === '' || $pass === '' || $first === '' || $last === '') {
            $res->json(['error' => 'email, password, first_name, last_name required'], 422);
            return;
        }
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        try {
            $id = $this->repo->create($email, $hash, $first, $last, in_array($role, ['admin', 'user']) ? $role : 'user');
            $res->json(['id' => $id], 201);
        } catch (\Throwable $e) {
            $res->json(['error' => 'email already exists?'], 409);
        }
    }

    public function update(Request $req, Response $res): void
    {
        if (!$this->ensureAdmin($req, $res)) return;
        $id = (int)($req->params['id'] ?? 0);
        $payload = [];
        foreach (['email', 'first_name', 'last_name', 'role'] as $f) {
            if (array_key_exists($f, $req->body)) {
                $payload[$f] = $req->body[$f];
            }
        }
        if (isset($req->body['password']) && $req->body['password'] !== '') {
            $payload['password_hash'] = password_hash((string)$req->body['password'], PASSWORD_BCRYPT);
        }
        if (!$payload) {
            $res->json(['error' => 'no fields'], 422);
            return;
        }
        $ok = $this->repo->update($id, $payload);
        if (!$ok) {
            $res->json(['error' => 'Not Found or nothing changed'], 404);
            return;
        }
        $res->json(['updated' => true]);
    }

    public function delete(Request $req, Response $res): void
    {
        if (!$this->ensureAdmin($req, $res)) return;
        $id = (int)($req->params['id'] ?? 0);
        $ok = $this->repo->delete($id);
        if (!$ok) {
            $res->json(['error' => 'Not Found'], 404);
            return;
        }
        $res->json(['deleted' => true]);
    }
    */
}
