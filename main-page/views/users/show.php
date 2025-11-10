<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Usuario #<?= (int)$user['id'] ?></h1>
    <div class="btn-group">
        <a class="btn btn-outline-secondary" href="/?r=users/index"><i class="bi bi-arrow-left"></i> Volver</a>
        <a class="btn btn-primary" href="/?r=users/index#edit-<?= (int)$user['id'] ?>"><i class="bi bi-pencil"></i> Editar</a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($user['email']) ?></dd>

            <dt class="col-sm-3">Rol</dt>
            <dd class="col-sm-9"><span class="badge text-bg-secondary"><?= htmlspecialchars($user['role']) ?></span></dd>

            <dt class="col-sm-3">Creado</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($user['created_at'] ?? '') ?></dd>
        </dl>
    </div>
</div>

<h2 class="h5">Tareas recientes</h2>
<table class="table table-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estado</th>
        </tr>
    </thead>
    <?php if (!empty($tasks)): ?>
        <tbody>
            <?php foreach ($tasks as $t): ?>
                <tr>
                    <td><a href="/?r=tasks/show&id=<?= (int)$t['id'] ?>">#<?= (int)$t['id'] ?></a></td>
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= htmlspecialchars($t['starts_at']) ?></td>
                    <td><?= htmlspecialchars($t['ends_at']) ?></td>
                    <td><?= $t['completed'] ? 'Completada' : 'Pendiente' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    <?php else: ?>
        <tbody>
            <tr>
                <td colspan="5" class="text-center text-muted">No hay tareas recientes.</td>
            </tr>
        </tbody>
    <?php endif; ?>
</table>
<?php $content = ob_get_clean();
require __DIR__ . '/../layout.php'; ?>