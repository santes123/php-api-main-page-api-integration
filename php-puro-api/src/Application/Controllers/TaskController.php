<?php

namespace App\Application\Controllers;

use App\App\{Request, Response};
use App\Infrastructure\Database;
use App\Infrastructure\Repositories\TaskRepository;

class TaskController
{
    private TaskRepository $repo;
    public function __construct(Database $db)
    {
        $this->repo = new TaskRepository($db);
    }

    public function index(Request $req, Response $res): void
    {
        $role = (string)($req->params['role'] ?? 'user');
        $uid  = (int)($req->params['user_id'] ?? 0);

        $page = max(1, (int)($req->query['page'] ?? 1));
        $per  = min(100, max(1, (int)($req->query['per_page'] ?? 20)));

        //recoger filtros
        $q         = isset($req->query['q']) ? trim((string)$req->query['q']) : null;
        $completed = isset($req->query['completed']) && $req->query['completed'] !== ''
            ? (string)$req->query['completed']   // "0" | "1"
            : null;

        if ($role === 'admin' && (($req->query['all'] ?? '') === '1' || isset($req->query['user_id']))) {
            $userFilter = isset($req->query['user_id']) ? (int)$req->query['user_id'] : null;
            //pasar filtros al repo también en modo admin
            $items = $this->repo->all($page, $per, $q, $completed, $userFilter);
        } else {
            //pasar q/completed al repo en rama usuario
            $items = $this->repo->allByUser($uid, $page, $per, $q, $completed);
        }

        $res->json(['data' => $items, 'page' => $page, 'per_page' => $per]);
    }

    public function show(Request $req, Response $res): void
    {
        $uid = (int)($req->params['user_id'] ?? 0);
        $id = (int)($req->params['id'] ?? 0);
        $it = $this->repo->find($uid, $id);
        if (!$it) {
            $res->json(['error' => 'Not Found'], 404);
            return;
        }
        $res->json(['data' => $it]);
    }

    public function create(Request $req, Response $res): void
    {
        $role = (string)($req->params['role'] ?? 'user');
        $uid  = (int)($req->params['user_id'] ?? 0);

        //validación de título
        $title = trim((string)($req->body['title'] ?? ''));
        if ($title === '') {
            $res->json(['error' => 'titulo requerido'], 422);
            return;
        }

        $desc = $req->body['description'] ?? null;
        $sa   = $req->body['starts_at'] ?? null;
        $ea   = $req->body['ends_at'] ?? null;
        $comp = (int)!!($req->body['completed'] ?? 0);

        //validación de fecha
        if (!$sa || !$ea) {
            $res->json(['error' => 'La fecha de inicio y fin son obligatorias'], 422);
            return;
        }
        if ($sa && $ea && ($ea < $sa)) {
            $res->json(['error' => 'La fecha fin no puede ser anterior al inicio'], 422);
            return;
        }

        // Si admin y viene user_id, usarlo; si no, su propio uid
        $ownerId = ($role === 'admin' && isset($req->body['user_id'])) ? (int)$req->body['user_id'] : $uid;

        $id = $this->repo->create($ownerId, $title, $desc, $sa, $ea, $comp);
        $res->json(['id' => $id], 201);
    }

    public function update(Request $req, Response $res): void
    {
        $role = (string)($req->params['role'] ?? 'user');
        $uid  = (int)($req->params['user_id'] ?? 0);
        $id   = (int)($req->params['id'] ?? 0);

        $payload = array_intersect_key(
            $req->body,
            array_flip(['title', 'description', 'starts_at', 'ends_at', 'completed', 'user_id'])
        );
        if (!$payload) {
            $res->json(['error' => 'No fields to update'], 422);
            return;
        }
        //validación de fecha
        if (!$req->body['starts_at'] || !$req->body['ends_at']) {
            $res->json(['error' => 'La fecha de inicio y fin son obligatorias'], 422);
            return;
        }
        if ($req->body['starts_at'] && $req->body['ends_at'] && ($req->body['ends_at'] < $req->body['starts_at'])) {
            $res->json(['error' => 'La fecha fin no puede ser anterior al inicio'], 422);
            return;
        }


        if ($role === 'admin') {
            //Admin edita por ID, sin filtro por user_id
            $exists = $this->repo->findById($id);
            if (!$exists) {
                $res->json(['error' => 'Not Found'], 404);
                return;
            }
            $ok = $this->repo->updateById($id, $payload);
        } else {
            //Usuario normal: sólo sus tareas
            $exists = $this->repo->find($uid, $id);
            if (!$exists) {
                $res->json(['error' => 'Not Found'], 404);
                return;
            }
            // No permitimos cambiar user_id al usuario normal
            unset($payload['user_id']);
            $ok = $this->repo->update($uid, $id, $payload);
        }

        if (!$ok) {
            $res->json(['error' => 'Not Found or nothing changed'], 404);
            return;
        }
        $res->json(['updated' => true]);
    }

    public function delete(Request $req, Response $res): void
    {
        $role = (string)($req->params['role'] ?? 'user');
        $uid  = (int)($req->params['user_id'] ?? 0);
        $id   = (int)($req->params['id'] ?? 0);

        if ($role === 'admin') {
            $exists = $this->repo->findById($id);
            if (!$exists) {
                $res->json(['error' => 'Not Found'], 404);
                return;
            }
            $ok = $this->repo->deleteById($id);
        } else {
            $exists = $this->repo->find($uid, $id);
            if (!$exists) {
                $res->json(['error' => 'Not Found'], 404);
                return;
            }
            $ok = $this->repo->delete($uid, $id);
        }

        if (!$ok) {
            $res->json(['error' => 'Not Found'], 404);
            return;
        }
        $res->json(['deleted' => true]);
    }
}
