<?php
require_once __DIR__ . '/../config/db.php';

class UserModel
{
    public static function findByEmail($email)
    {
        $st = db()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
        $st->execute([$email]);
        return $st->fetch();
    }
    public static function all($search = null, $limit = 20, $offset = 0)
    {
        $sql = 'SELECT * FROM users WHERE 1';
        $args = [];
        if ($search) {
            $sql .= ' AND (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)';
            $args = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= ' ORDER BY id DESC LIMIT ? OFFSET ?';

        $args[] = $limit;
        $args[] = $offset;

        $st = db()->prepare($sql);
        $st->execute($args);

        return $st->fetchAll();
    }
    public static function count($search = null)
    {
        $sql = 'SELECT COUNT(*) c FROM users WHERE 1';
        $args = [];
        if ($search) {
            $sql .= ' AND (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)';
            $args = ["%$search%", "%$search%", "%$search%"];
        }
        $st = db()->prepare($sql);
        $st->execute($args);
        return (int)$st->fetch()['c'];
    }
    public static function find($id)
    {
        $st = db()->prepare('SELECT * FROM users WHERE id=?');
        $st->execute([$id]);
        return $st->fetch();
    }
    public static function create($data)
    {
        $st = db()->prepare('INSERT INTO users(email,password_hash,first_name,last_name,role) VALUES(?,?,?,?,?)');
        $st->execute([$data['email'], $data['password_hash'], $data['first_name'], $data['last_name'], $data['role'] ?? 'admin']);
        return db()->lastInsertId();
    }
    public static function update($id, $data)
    {
        $fields = ['email=?', 'first_name=?', 'last_name=?', 'role=?'];
        $args = [$data['email'], $data['first_name'], $data['last_name'], $data['role']];
        if (!empty($data['password_hash'])) {
            $fields[] = 'password_hash=?';
            $args[] = $data['password_hash'];
        }
        $args[] = $id;
        $st = db()->prepare('UPDATE users SET ' . implode(',', $fields) . ' WHERE id=?');
        return $st->execute($args);
    }
    public static function delete($id)
    {
        $st = db()->prepare('DELETE FROM users WHERE id=?');
        return $st->execute([$id]);
    }
}
