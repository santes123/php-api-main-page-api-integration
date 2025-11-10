<?php ob_start();
require_once __DIR__ . '/../../core/auth.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Usuarios</h1>
    <button class="btn btn-success btn-new-user" type="button" data-bs-toggle="modal" data-bs-target="#mdlUser">Nuevo usuario</button>
</div>

<form class="row g-2 mb-3" action="/?r=users/index" method="get">
    <input type="hidden" name="r" value="users/index"> <!-- por si cambias acción -->
    <div class="col-auto">
        <input class="form-control" type="search" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar...">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary">Buscar</button>
    </div>
</form>

<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Nombre</th>
            <th>Rol</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr data-id="<?= $u['id'] ?>">
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                <td><span class="badge bg-secondary"><?= $u['role'] ?></span></td>
                <td class="text-end">
                    <a href="/?r=users/show&id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-info">
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
<div class="modal fade" id="mdlUser" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formUser">
            <div class="modal-header">
                <h5 class="modal-title">Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
                <input type="hidden" name="id" value="">
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Nombre</label>
                        <input class="form-control" name="first_name" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Apellidos</label>
                        <input class="form-control" name="last_name" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Rol</label>
                    <select class="form-select" name="role">
                        <option value="admin">admin</option>
                        <option value="user">user</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Contraseña (dejar vacío para no cambiar)</label>
                    <input class="form-control" type="password" name="password">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="/assets/js/users.js"></script>
<?php $content = ob_get_clean();
require __DIR__ . '/../layout.php'; ?>