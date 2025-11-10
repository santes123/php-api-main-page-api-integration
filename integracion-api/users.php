<?php
require __DIR__ . '/env.php';
$BASE_URL = env('BASE_URL', '');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>API Demo · Usuarios</title>
  <link rel="stylesheet" href="css/users.css">
</head>

<body>
  <header>
    <strong>Usuarios</strong>
    <nav>
      <a href="tasks.php">Tasks</a>
      <button id="btnLogout">Salir</button>
    </nav>
  </header>

  <main>
    <section class="card">
      <div class="row">
        <label>Página</label><input id="page" type="number" value="1" style="width:80px" />
        <label>Por página</label><input id="per" type="number" value="50" style="width:80px" />
        <button id="btnReload">Recargar</button>
        <span id="msg" class="muted"></span>
      </div>
    </section>

    <section class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Nombre</th>
            <th>Creado</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
      <div id="totals" class="muted" style="margin-top:8px"></div>
    </section>
  </main>
  <script>
    const BASE_URL = <?= json_encode($BASE_URL) ?>; // <- viene del .env
  </script>
  <script src="js/users.js"></script>

</body>

</html>