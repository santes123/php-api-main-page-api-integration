<?php 
  require __DIR__ . '/env.php';
  $BASE_URL = env('BASE_URL', ''); 
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>API Demo · Tasks</title>
  <link rel="stylesheet" href="css/tasks.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <header>
    <strong>Tasks</strong>
    <nav>
      <a href="users.php">Usuarios</a>
      <button id="btnNew">Nueva</button>
      <button id="btnLogout">Salir</button>
    </nav>
  </header>

  <main>
    <section class="card">
      <div class="row">
        <label>Buscar</label><input id="q" placeholder="título o descripción" />
        <label>Completada</label>
        <select id="completed">
          <option value="">Todas</option>
          <option value="0">Pendientes</option>
          <option value="1">Completadas</option>
        </select>

        <label class="muted">
          <input type="checkbox" id="seeAll"> Ver todas (admin)
        </label>


        <label>Página</label><input id="page" type="number" value="1" style="width: 80px" />
        <label>Por página</label><input id="per" type="number" value="10" style="width: 80px" />
        <button id="btnReload">Recargar</button>
        <span id="msg" class="muted"></span>
      </div>
    </section>

    <section class="card">
      <div id="list" class="list"></div>
    </section>
  </main>

  <!-- Dialog nativo -->
  <dialog id="dlg">
    <div class="modal">
      <h3 id="dlgTitle">Crear tarea</h3>

      <label>Título</label>
      <input id="f_title" placeholder="Título" style="width:100%;margin:6px 0" />

      <label>Descripción</label>
      <textarea id="f_description" placeholder="Descripción" style="width:100%;margin:6px 0"></textarea>

      <div class="row">
        <div style="flex:1">
          <label>Inicio</label>
          <input id="f_starts" type="datetime-local" style="width:100%" />
        </div>
        <div style="flex:1">
          <label>Fin</label>
          <input id="f_ends" type="datetime-local" style="width:100%" />
        </div>
      </div>

      <div class="row" style="margin-top:6px">
        <label><input id="f_completed" type="checkbox" /> Completada</label>
      </div>

      <div id="wrap_user" style="display:none; margin-top:6px">
        <label>Usuario</label>
        <select id="f_user_id" style="width:100%"></select>
      </div>

      <div class="footer">
        <button id="btnCancel">Cancelar</button>
        <button id="btnSave" class="primary">Guardar</button>
      </div>
    </div>
  </dialog>

  <script>
    const BASE_URL = <?= json_encode($BASE_URL) ?>; // <- viene del .env
  </script>
  <script src="js/alerts.js"></script>
  <script src="js/tasks.js"></script>
</body>

</html>