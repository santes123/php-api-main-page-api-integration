<?php
  require __DIR__ . '/env.php';
  $BASE_URL = env('BASE_URL', '');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <title>Redirect</title>
</head>

<body>
  <script>
    const BASE_URL = <?= json_encode($BASE_URL) ?>; // <- viene del .env
  </script>
  <script src="js/index.js"></script>
</body>

</html>