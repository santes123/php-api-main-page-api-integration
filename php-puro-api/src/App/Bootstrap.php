<?php

namespace App\App;

class Bootstrap
{
    //carga los datos del .env
    public function loadEnv(string $basePath): void
    {
        $file = $basePath . '.env';
        if (!is_file($file)) return;
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            $_ENV[$k] = $v;
            putenv("$k=$v");
        }
    }
}
