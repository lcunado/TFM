<?php
require_once __DIR__ . '/../../private/config.php';

$usuario = "admin";
$pass = "1234"; // Contraseña inicial

$hash = password_hash($pass, PASSWORD_DEFAULT);

// Crear administrador en la BD
$sql = "INSERT INTO admin (usuario, password_hash) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $usuario, $hash);
$stmt->execute();

echo "Administrador creado correctamente.";

