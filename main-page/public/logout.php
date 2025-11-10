<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../core/auth.php';
logout_user();
header('Location: /?r=auth/login');
