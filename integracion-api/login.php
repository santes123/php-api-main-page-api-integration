<?php 
  require __DIR__ . '/env.php';
  $BASE_URL = env('BASE_URL', ''); 
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>API Demo · Login</title>
  <link rel="stylesheet" href="css/login.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <main>
    <h1>Accede</h1>
    <input id="email" placeholder="email" value="admin@example.com" />
    <input id="password" type="password" placeholder="password" />
    <button id="btn">Entrar</button>
    <div id="msg" class="muted"></div>
  </main>

  <script>
    const BASE_URL = <?= json_encode($BASE_URL) ?>; // <- viene del .env
  </script>
  <script src="js/alerts.js"></script>
  <script src="js/login.js"></script>

</body>

</html>