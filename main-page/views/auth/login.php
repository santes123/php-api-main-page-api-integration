<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Iniciar sesión</h1>
                <form id="formLogin">
                    <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#formLogin').on('submit', function(e) {
        e.preventDefault();
        $.post('/?r=auth/login', $(this).serialize())
            .done(() => location.href = '/?r=tasks/index')
            .fail(x => showToast(x.responseJSON?.error || 'Error', "error"));
    });
</script>
<?php $content = ob_get_clean();
require __DIR__ . '/../layout.php'; ?>