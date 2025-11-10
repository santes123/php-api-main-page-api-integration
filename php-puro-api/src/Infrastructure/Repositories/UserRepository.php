<?php

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Database;
use PDO;

class UserRepository
{
    public function __construct(private Database $db) {}

    public function list(int $page = 1, int $per = 20): array
    {
        $off = ($page - 1) * $per;
        $st = $this->db->pdo->prepare(
            'SELECT id, email, first_name, last_name, role, created_at, updated_at
             FROM users ORDER BY id DESC LIMIT :l OFFSET :o'
        );
        $st->bindValue(':l', $per, PDO::PARAM_INT);
        $st->bindValue(':o', $off, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function count(): int
    {
        return (int)$this->db->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
    
    //auxiliar para encontrar un usuario
    public function find(int $id): ?array
    {
        $st = $this->db->pdo->prepare(
            'SELECT id, email, first_name, last_name, role, created_at, updated_at
             FROM users WHERE id=:id'
        );
        $st->execute([':id' => $id]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public function create(string $email, string $passwordHash, string $first, string $last, string $role = 'user'): int
    {
        $st = $this->db->pdo->prepare(
            'INSERT INTO users (email, password_hash, first_name, last_name, role, created_at, updated_at)
             VALUES (:e,:p,:f,:l,:r, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)'
        );
        $st->execute([':e' => $email, ':p' => $passwordHash, ':f' => $first, ':l' => $last, ':r' => $role]);
        return (int)$this->db->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = ['email', 'first_name', 'last_name', 'role', 'password_hash'];
        $sets = [];
        $p = [':id' => $id];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f=:$f";
                $p[":$f"] = $data[$f];
            }
        }
        if (!$sets) return false;
        $sql = 'UPDATE users SET ' . implode(',', $sets) . ', updated_at=CURRENT_TIMESTAMP WHERE id=:id';
        $st = $this->db->pdo->prepare($sql);
        return $st->execute($p);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->pdo->prepare('DELETE FROM users WHERE id=:id');
        return $st->execute([':id' => $id]);
    }
}
