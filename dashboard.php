<?php
session_start();
if (empty($_SESSION["auth"])) {
  header("Location: index.html");
  exit;
}
$email = $_SESSION["email"] ?? "usuario";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel - MiSitio</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="header">
        <div class="brand">
          <h1>Panel</h1>
          <p>Sesión activa: <?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?></p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
          <a class="badge" href="producto/3">Probar /producto/3</a>
          <a class="btn btn-ghost" href="privado/">/privado/</a>
          <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
        </div>
      </div>

      <div class="main">
        <div class="pane hero">
          <h2 style="margin:0 0 8px 0;font-size:20px;">CRUD de Productos (vía API)</h2>
          <p style="margin:0;color:var(--muted);line-height:1.55;">
            El archivo <b>admin/crud.php</b> está bloqueado por .htaccess para acceso directo, pero el sistema lo usa mediante include.
          </p>

          <div id="status" class="alert" style="margin-top:14px;">Listo para operar.</div>

          <div style="margin-top:18px;">
            <table class="table">
              <thead>
                <tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Acciones</th></tr>
              </thead>
              <tbody id="rows"></tbody>
            </table>
          </div>
        </div>

        <div class="pane">
          <h2 style="margin:0 0 12px 0;font-size:18px;">Crear / Actualizar</h2>

          <form id="form" class="form">
            <div class="row">
              <div>
                <div class="label">ID (vacío para crear)</div>
                <input class="input" name="id" type="number" min="1" placeholder="Ej: 3" />
              </div>
              <div>
                <div class="label">Precio</div>
                <input class="input" name="precio" type="number" step="0.01" min="0" placeholder="Ej: 9.99" required />
              </div>
            </div>

            <div>
              <div class="label">Nombre</div>
              <input class="input" name="nombre" type="text" placeholder="Ej: Producto demo" required />
            </div>

            <div class="row">
              <button class="btn" type="submit">Guardar</button>
              <button class="btn btn-ghost" id="btnClear" type="button">Limpiar</button>
            </div>

            <p class="note">Para eliminar, usa el botón “Eliminar” en la tabla.</p>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    const rows = document.getElementById("rows");
    const statusEl = document.getElementById("status");
    const form = document.getElementById("form");
    const btnClear = document.getElementById("btnClear");

    function setStatus(msg, type){
      statusEl.className = "alert " + (type || "");
      statusEl.textContent = msg;
    }

    async function load(){
      const r = await fetch("api/productos.php");
      const data = await r.json();
      if (!r.ok) {
        setStatus(data.error || "Error al cargar", "error");
        return;
      }
      rows.innerHTML = data.map(p => `
        <tr>
          <td>${p.id}</td>
          <td>${escapeHtml(p.nombre)}</td>
          <td>$${Number(p.precio).toFixed(2)}</td>
          <td style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn btn-ghost" onclick="fill(${p.id}, '${escapeAttr(p.nombre)}', ${p.precio})" type="button">Editar</button>
            <button class="btn btn-danger" onclick="del(${p.id})" type="button">Eliminar</button>
            <a class="btn btn-ghost" href="producto/${p.id}">Ver</a>
          </td>
        </tr>
      `).join("");
      setStatus("Productos cargados: " + data.length, "ok");
    }

    function fill(id, nombre, precio){
      form.id.value = id;
      form.nombre.value = nombre;
      form.precio.value = precio;
      setStatus("Editando producto ID " + id, "ok");
    }

    async function del(id){
      const fd = new FormData();
      fd.append("accion", "delete");
      fd.append("id", id);
      const r = await fetch("api/productos.php", { method:"POST", body: fd });
      const data = await r.json();
      if (!r.ok) {
        setStatus(data.error || "Error al eliminar", "error");
        return;
      }
      await load();
      setStatus("Producto eliminado: " + id, "ok");
    }

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const id = form.id.value.trim();
      const fd = new FormData();
      if (id) {
        fd.append("accion", "update");
        fd.append("id", id);
      } else {
        fd.append("accion", "create");
      }
      fd.append("nombre", form.nombre.value.trim());
      fd.append("precio", form.precio.value.trim());

      const r = await fetch("api/productos.php", { method:"POST", body: fd });
      const data = await r.json();
      if (!r.ok) {
        setStatus(data.error || "Error al guardar", "error");
        return;
      }
      btnClear.click();
      await load();
      setStatus("Operación realizada correctamente.", "ok");
    });

    btnClear.addEventListener("click", () => {
      form.reset();
      setStatus("Listo para operar.", "");
    });

    function escapeHtml(s){
      return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }
    function escapeAttr(s){
      return String(s).replace(/\\/g,'\\\\').replace(/'/g,"\\'");
    }

    load();
  </script>
</body>
</html>
