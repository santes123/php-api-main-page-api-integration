<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PDO;
use App\Infrastructure\Repositories\TaskRepository;
use App\Infrastructure\Database;

class TaskRepositoryTest extends TestCase
{
    private TaskRepository $repo;
    private PDO $pdo;
    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("
            CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                starts_at TEXT NULL,
                ends_at TEXT NULL,
                completed INTEGER NOT NULL DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
        ");
        $db = new class($this->pdo) extends Database {
            public function __construct(private PDO $p)
            {
                $this->pdo = $p;
            }
        };
        $this->repo = new TaskRepository($db);
    }
    public function testCreateAndFind(): void
    {
        $id = $this->repo->create(1, 'Test', 'Desc');
        $row = $this->repo->find(1, $id);
        $this->assertNotNull($row);
        $this->assertEquals('Test', $row['title']);
    }
}
