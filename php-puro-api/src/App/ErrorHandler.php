<?php

namespace App\App;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler(function (\Throwable $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        });
    }
}
