<?php
require_once __DIR__ . '/../config/db.php';

class TaskModel
{
    public static function all($filters = [], $limit = 20, $offset = 0)
    {
        $sql = 'SELECT t.*, u.email AS user_email FROM tasks t JOIN users u ON u.id=t.user_id WHERE 1';
        $args = [];
        if (!empty($filters['user_id'])) {
            $sql .= ' AND t.user_id=?';
            $args[] = $filters['user_id'];
        }
        if (isset($filters['completed'])) {
            $sql .= ' AND t.completed=?';
            $args[] = (int)$filters['completed'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (t.title LIKE ? OR t.description LIKE ?)';
            $args[] = "%{$filters['search']}%";
            $args[] = "%{$filters['search']}%";
        }
        $sql .= ' ORDER BY t.id DESC LIMIT ? OFFSET ?';

        $st = db()->prepare($sql);
        $args[] = $limit;
        $args[] = $offset;

        $st->execute($args);
        return $st->fetchAll();
    }
    public static function count($filters = [])
    {
        $sql = 'SELECT COUNT(*) c FROM tasks t WHERE 1';
        $args = [];
        if (!empty($filters['user_id'])) {
            $sql .= ' AND t.user_id=?';
            $args[] = $filters['user_id'];
        }
        if (isset($filters['completed'])) {
            $sql .= ' AND t.completed=?';
            $args[] = (int)$filters['completed'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (t.title LIKE ? OR t.description LIKE ?)';
            $args[] = "%{$filters['search']}%";
            $args[] = "%{$filters['search']}%";
        }
        $st = db()->prepare($sql);
        $st->execute($args);
        return (int)$st->fetch()['c'];
    }
    public static function find($id)
    {
        $st = db()->prepare('SELECT * FROM tasks WHERE id=?');
        $st->execute([$id]);
        return $st->fetch();
    }
    public static function create($data)
    {
        $st = db()->prepare('INSERT INTO tasks(user_id,title,description,starts_at,ends_at,completed) VALUES(?,?,?,?,?,?)');
        $st->execute([$data['user_id'], $data['title'], $data['description'], $data['starts_at'], $data['ends_at'], (int)$data['completed']]);
        return db()->lastInsertId();
    }
    public static function update($id, $data)
    {
        $st = db()->prepare('UPDATE tasks SET user_id=?, title=?, description=?, starts_at=?, ends_at=?, completed=? WHERE id=?');
        return $st->execute([$data['user_id'], $data['title'], $data['description'], $data['starts_at'], $data['ends_at'], (int)$data['completed'], $id]);
    }
    public static function delete($id)
    {
        $st = db()->prepare('DELETE FROM tasks WHERE id=?');
        return $st->execute([$id]);
    }
}
