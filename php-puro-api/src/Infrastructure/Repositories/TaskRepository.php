<?php

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Database;
use PDO;

class TaskRepository
{
    public function __construct(private Database $db) {}

    //devuelve todos los resultados con filtros
    public function all(int $page = 1, int $per = 20, ?string $q = null, ?string $completed = null, ?int $userFilter = null): array
    {
        $off = ($page - 1) * $per;
        $where = [];
        $p = [];
        if ($userFilter !== null) {
            $where[] = 'user_id = :u';
            $p[':u'] = $userFilter;
        }
        if ($q !== null && $q !== '') {
            $where[] = '(title LIKE :q OR description LIKE :q)';
            $p[':q'] = "%{$q}%";
        }
        if ($completed !== null && $completed !== '') {
            $where[] = 'completed = :c';
            $p[':c'] = (int)!!$completed;
        }
        $sql = 'SELECT id,user_id,title,description,starts_at,ends_at,completed,created_at,updated_at FROM tasks'
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY id DESC LIMIT :l OFFSET :o';
        $st = $this->db->pdo->prepare($sql);
        foreach ($p as $k => $v) {
            $st->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $st->bindValue(':l', $per, PDO::PARAM_INT);
        $st->bindValue(':o', $off, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    // devuelve los resultados de un solo usuario con filtros
    public function allByUser(int $userId, int $page = 1, int $perPage = 20, ?string $q = null, ?string $completed = null): array
    {
        $offset = ($page - 1) * $perPage;

        $params = [':uid' => $userId];
        $extra  = $this->buildFilters($q, $completed, $params);

        $sql = 'SELECT id,user_id,title,description,starts_at,ends_at,completed,created_at,updated_at
                FROM tasks
                WHERE user_id = :uid' . $extra . '
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset';

        $st = $this->db->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v, is_int($v) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $st->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    //auxiliar para encontrar un usuario
    public function find(int $userId, int $id): ?array
    {
        $st = $this->db->pdo->prepare(
            'SELECT id, user_id, title, description, starts_at, ends_at, completed, created_at, updated_at
             FROM tasks WHERE id=:id AND user_id=:u'
        );
        $st->execute([':id' => $id, ':u' => $userId]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public function create(
        int $userId,
        string $title,
        ?string $description,
        ?string $startsAt = null,
        ?string $endsAt   = null,
        int $completed    = 0
    ): int {
        $st = $this->db->pdo->prepare(
            'INSERT INTO tasks (user_id, title, description, starts_at, ends_at, completed, created_at, updated_at)
             VALUES (:u,:t,:d,:sa,:ea,:c,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)'
        );
        $st->execute([
            ':u' => $userId,
            ':t' => $title,
            ':d' => $description,
            ':sa' => $startsAt,
            ':ea' => $endsAt,
            ':c' => $completed ? 1 : 0
        ]);
        return (int)$this->db->pdo->lastInsertId();
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $map = ['title', 'description', 'starts_at', 'ends_at', 'completed'];
        $sets = [];
        $p = [':id' => $id, ':u' => $userId];
        foreach ($map as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f=:$f";
                $p[":$f"] = ($f === 'completed') ? (int)(!!$data[$f]) : $data[$f];
            }
        }
        if (!$sets) return false;
        $sql = 'UPDATE tasks SET ' . implode(',', $sets) . ', updated_at=CURRENT_TIMESTAMP WHERE id=:id AND user_id=:u';
        $st = $this->db->pdo->prepare($sql);
        return $st->execute($p);
    }

    public function delete(int $userId, int $id): bool
    {
        $st = $this->db->pdo->prepare('DELETE FROM tasks WHERE id=:id AND user_id=:u');
        return $st->execute([':id' => $id, ':u' => $userId]);
    }


    //metodos auxiliares (usados por admins)
    public function findById(int $id): ?array
    {
        $st = $this->db->pdo->prepare(
            'SELECT id,user_id,title,description,starts_at,ends_at,completed,created_at,updated_at FROM tasks WHERE id=:id'
        );
        $st->execute([':id' => $id]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public function updateById(int $id, array $data): bool
    {
        $allowed = ['title', 'description', 'starts_at', 'ends_at', 'completed', 'user_id'];
        $sets = [];
        $p = [':id' => $id];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f=:$f";
                $p[":$f"] = ($f === 'completed') ? (int)!!$data[$f] : $data[$f];
            }
        }
        if (!$sets) return false;
        $sql = 'UPDATE tasks SET ' . implode(',', $sets) . ', updated_at=CURRENT_TIMESTAMP WHERE id=:id';
        $st = $this->db->pdo->prepare($sql);
        $st->execute($p);
        return $st->rowCount() > 0;
    }

    public function deleteById(int $id): bool
    {
        $st = $this->db->pdo->prepare('DELETE FROM tasks WHERE id=:id');
        $st->execute([':id' => $id]);
        return $st->rowCount() > 0;
    }

    //utilidad para construir WHERE + params
    private function buildFilters(?string $q, ?string $completed, array &$params): string
    {
        $where = [];

        if ($q !== null && $q !== '') {
            $where[] = '(title LIKE :q OR description LIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }

        if ($completed !== null && $completed !== '') {
            // Esperamos "0" o "1"
            $where[] = 'completed = :c';
            $params[':c'] = (int)!!$completed;
        }

        return $where ? (' AND ' . implode(' AND ', $where)) : '';
    }
}
