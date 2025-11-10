<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\App\Bootstrap;
use App\Config\Config;
use App\Infrastructure\Database;

$boot = new Bootstrap();
$boot->loadEnv(__DIR__ . '/../');
$db = new Database(new Config());
$files = glob(__DIR__ . '/../migrations/*.sql');
sort($files);
foreach ($files as $f) {
    echo "\n> Applying " . basename($f) . "\n";
    $sql = file_get_contents($f);
    $db->pdo->exec($sql);
}
echo "\nAll migrations applied.\n";
