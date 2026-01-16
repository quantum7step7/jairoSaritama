<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if (empty($_SESSION["auth"])) {
  http_response_code(401);
  echo json_encode(["error" => "No autorizado"]);
  exit;
}

require_once __DIR__ . "/../admin/crud.php";

try {
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["id"])) {
      $id = (int)$_GET["id"];
      $p = obtenerProducto($id);
      if (!$p) {
        http_response_code(404);
        echo json_encode(["error" => "No encontrado"]);
        exit;
      }
      echo json_encode($p);
      exit;
    }
    echo json_encode(listarProductos());
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    if ($accion === "create") {
      $nombre = trim($_POST["nombre"] ?? "");
      $precio = (float)($_POST["precio"] ?? 0);
      if ($nombre === "") {
        http_response_code(422);
        echo json_encode(["error" => "Nombre requerido"]);
        exit;
      }
      $id = crearProducto($nombre, $precio);
      echo json_encode(["ok" => true, "id" => $id]);
      exit;
    }

    if ($accion === "update") {
      $id = (int)($_POST["id"] ?? 0);
      $nombre = trim($_POST["nombre"] ?? "");
      $precio = (float)($_POST["precio"] ?? 0);
      if ($id <= 0 || $nombre === "") {
        http_response_code(422);
        echo json_encode(["error" => "Datos inválidos"]);
        exit;
      }
      actualizarProducto($id, $nombre, $precio);
      echo json_encode(["ok" => true]);
      exit;
    }

    if ($accion === "delete") {
      $id = (int)($_POST["id"] ?? 0);
      if ($id <= 0) {
        http_response_code(422);
        echo json_encode(["error" => "ID inválido"]);
        exit;
      }
      eliminarProducto($id);
      echo json_encode(["ok" => true]);
      exit;
    }

    http_response_code(400);
    echo json_encode(["error" => "Acción inválida"]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Método no permitido"]);
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Error del servidor", "detalle" => $e->getMessage()]);
  exit;
}
