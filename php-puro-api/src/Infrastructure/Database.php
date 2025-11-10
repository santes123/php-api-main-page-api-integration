<?php

namespace App\Infrastructure;

use App\Config\Config;
use PDO;

//clase para la conexion de bbdd
class Database
{
    public PDO $pdo;
    public function __construct(Config $c)
    {
        $this->pdo = new PDO($c->dsn(), $c->user(), $c->pass(), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
