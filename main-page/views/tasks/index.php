<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Tareas</h1>
    <button class="btn btn-success btn-new-task" type="button" data-bs-toggle="modal" data-bs-target="#mdlTask">Nueva tarea</button>
</div>

<form class="row g-2 mb-3" action="/?r=tasks/index" method="get">
    <input type="hidden" name="r" value="tasks/index">
    <div class="col-auto">
        <input class="form-control" type="search" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar por título o descripción">
    </div>
    <div class="col-auto">
        <select class="form-select" name="completed">
            <option value="">Todas</option>
            <option value="0" <?= (isset($_GET['completed']) && $_GET['completed'] === '0') ? 'selected' : ''; ?>>Pendientes</option>
            <option value="1" <?= (isset($_GET['completed']) && $_GET['completed'] === '1') ? 'selected' : ''; ?>>Completadas</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary">Filtrar</button>
    </div>
</form>

<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <?php if (is_admin()): ?><th>Usuario</th><?php endif; ?>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tasks as $t): ?>
            <tr data-id="<?= $t['id'] ?>" data-description="<?= htmlspecialchars($t['description'] ?? '', ENT_QUOTES) ?>">
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['title']) ?></td>
                <?php if (is_admin()): ?>



                    <td><?= htmlspecialchars($t['user_email'] ?? '') ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($t['starts_at']) ?></td>
                <td><?= htmlspecialchars($t['ends_at']) ?></td>
                <td>
                    <span class="badge <?= $t['completed'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                        <?= $t['completed'] ? 'Completada' : 'Pendiente' ?>
                    </span>
                </td>
                <td class="text-end">
                    <a href="/?r=tasks/show&id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button class="btn btn-sm btn-primary btn-edit">Editar</button>
                    <button class="btn btn-sm btn-danger btn-del">Borrar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="mdlTask" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formTask">
            <div class="modal-header">
                <h5 class="modal-title">Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
                <input type="hidden" name="id">
                <?php if (is_admin()): ?>
                    <div class="mb-2">
                        <label class="form-label">Usuario</label>
                        <select class="form-select" name="user_id" required>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['email']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="mb-2">
                    <label class="form-label">Título</label>
                    <input class="form-control" name="title" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Inicio</label>
                        <input class="form-control" type="datetime-local" name="starts_at" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Fin</label>
                        <input class="form-control" type="datetime-local" name="ends_at" required>
                    </div>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="1" id="chkCompleted" name="completed">
                    <label class="form-check-label" for="chkCompleted">Marcar como completada</label>
                </div>
                <?php if (!is_admin()): ?>
                    <!-- Para usuarios normales, user_id va oculto con su propio id -->
                    <input type="hidden" name="user_id" value="<?= current_user()['id'] ?>">
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="/assets/js/tasks.js"></script>
<?php $content = ob_get_clean();
require __DIR__ . '/../layout.php'; ?>