<?php
// Lee y vacía los mensajes flash guardados en $_SESSION['flash']
$flashes = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

foreach ($flashes as $f):
    $type = $f['type'] ?? 'info';
    $msg  = $f['msg']  ?? '';
    if (!$msg) continue;
?>
    <div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endforeach; ?>