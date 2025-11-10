<?php
require_once __DIR__ . '/auth.php';

function require_login()
{
    if (!is_logged_in()) {
        header('Location: /?r=auth/login');
        exit;
    }
}
function require_admin()
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
