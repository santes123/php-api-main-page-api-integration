<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Detalle de tarea #<?= (int)$task['id'] ?></h1>
    <div class="btn-group">
        <a class="btn btn-outline-secondary" href="/?r=tasks/index"><i class="bi bi-arrow-left"></i> Volver</a>
        <a class="btn btn-primary" href="/?r=tasks/index#edit-<?= (int)$task['id'] ?>"><i class="bi bi-pencil"></i> Editar</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Título</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($task['title']) ?></dd>

            <dt class="col-sm-3">Usuario</dt>
            <dd class="col-sm-9"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?> <span class="text-muted">(<?= htmlspecialchars($user['email'] ?? '') ?>)</span></dd>

            <dt class="col-sm-3">Descripción</dt>
            <dd class="col-sm-9">
                <pre class="mb-0"><?= htmlspecialchars($task['description'] ?? '') ?></pre>
            </dd>

            <dt class="col-sm-3">Inicio</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($task['starts_at'] ?? '') ?></dd>

            <dt class="col-sm-3">Fin</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($task['ends_at'] ?? '') ?></dd>

            <dt class="col-sm-3">Estado</dt>
            <dd class="col-sm-9">
                <span class="badge <?= $task['completed'] ? 'text-bg-success' : 'text-bg-warning' ?>">
                    <?= $task['completed'] ? 'Completada' : 'Pendiente' ?>
                </span>
            </dd>
        </dl>
    </div>
</div>
<?php $content = ob_get_clean();
require __DIR__ . '/../layout.php'; ?>