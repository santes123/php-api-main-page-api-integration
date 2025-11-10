<?php
require_once __DIR__ . '/../../core/auth.php';

// Utilidad sencilla para marcar activo según ruta (?r=controlador/accion)
$current = $_GET['r'] ?? '';
function isActive(string $prefix): string
{
    global $current;
    return str_starts_with($current, $prefix) ? 'active' : '';
}
$user = current_user();
//para generar dinamicamente la url del demo (comentado ahora mismo)
/*
function getApiDemoUrl(): string {
    $localUrl = 'http://localhost:5500';
    $remoteUrl = 'http://api-integration.local/login.php';

    // Intentamos abrir conexión con localhost:5500
    $fp = @fsockopen('localhost', 5500, $errno, $errstr, 0.3);
    if ($fp) {
        fclose($fp);
        return $localUrl; // Está activo
    }

    return $remoteUrl; // No responde, usamos el remoto
}
*/
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="/?r=tasks/index">
            <i class="bi bi-check2-square me-1"></i>Gestor de Tareas
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= isActive('tasks/') ?>" href="/?r=tasks/index">
                        <i class="bi bi-list-task me-1"></i>Tareas
                    </a>
                </li>
                <?php if (is_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('users/') ?>" href="/?r=users/index">
                            <i class="bi bi-people me-1"></i>Usuarios
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="/docs.html" target="_blank">📄 Documentación API</a>
                </li>
                <!-- depende de donde se ejecute-->
                <!-- <li class="nav-item">
                    <a class="nav-link" href="" target="_blank">
                        <i class="bi bi-braces me-1"></i>API Demo
                    </a>
                </li> -->

            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary small">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    <span class="text-muted">· <?= htmlspecialchars($user['email']) ?></span>
                    <?php if (is_admin()): ?>
                        <span class="badge text-bg-secondary ms-1">admin</span>
                    <?php endif; ?>
                </span>
                <a class="btn btn-outline-secondary btn-sm" href="/logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </a>
            </div>
        </div>
    </div>
</nav>
