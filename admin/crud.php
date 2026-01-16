<?php
function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  $host = "127.0.0.1";
  $db = "misitio_db";
  $user = "root";
  $pass = "";
  $charset = "utf8mb4";

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ];

  $pdo = new PDO($dsn, $user, $pass, $opts);
  return $pdo;
}

function listarProductos(): array {
  $st = db()->query("SELECT id, nombre, precio FROM productos ORDER BY id DESC");
  return $st->fetchAll();
}

function obtenerProducto(int $id): ?array {
  $st = db()->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
  $st->execute([$id]);
  $row = $st->fetch();
  return $row ?: null;
}

function crearProducto(string $nombre, float $precio): int {
  $st = db()->prepare("INSERT INTO productos (nombre, precio) VALUES (?, ?)");
  $st->execute([$nombre, $precio]);
  return (int)db()->lastInsertId();
}

function actualizarProducto(int $id, string $nombre, float $precio): bool {
  $st = db()->prepare("UPDATE productos SET nombre = ?, precio = ? WHERE id = ?");
  return $st->execute([$nombre, $precio, $id]);
}

function eliminarProducto(int $id): bool {
  $st = db()->prepare("DELETE FROM productos WHERE id = ?");
  return $st->execute([$id]);
}
