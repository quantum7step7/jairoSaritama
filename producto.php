<?php
require_once __DIR__ . "/admin/crud.php";

$id = (int)($_GET["id"] ?? 0);
$p = $id > 0 ? obtenerProducto($id) : null;

if (!$p) {
  http_response_code(404);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Producto</title>
  <link rel="stylesheet" href="/misitio/styles.css" />
</head>
<body>
  <div class="wrap">
    <div class="card" style="max-width:820px;">
      <div class="header">
        <div class="brand">
          <h1>Producto</h1>
          <p>Prueba de URL amigable</p>
        </div>
        <a class="btn btn-ghost" href="/misitio/index.html">Volver</a>
      </div>

      <div class="pane">
        <?php if ($p): ?>
          <div class="alert ok">Mostrando ID: <?php echo (int)$p["id"]; ?></div>
          <h2 style="margin:14px 0 6px 0;"><?php echo htmlspecialchars($p["nombre"], ENT_QUOTES, "UTF-8"); ?></h2>
          <p style="margin:0;color:var(--muted);">Precio: $<?php echo number_format((float)$p["precio"], 2); ?></p>

          <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
            <a class="btn" href="/misitio/producto.php?id=<?php echo (int)$p["id"]; ?>">URL antigua</a>
            <a class="btn btn-ghost" href="/misitio/producto/<?php echo (int)$p["id"]; ?>">URL amigable</a>
          </div>
        <?php else: ?>
          <div class="alert error">Producto no encontrado. Prueba con /producto/3 si existe en tu BD.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
