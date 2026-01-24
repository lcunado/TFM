<?php
require_once __DIR__ . '/../../private/config.php';

$usuario = "admin";
$pass = "1234"; // Contraseña inicial

// Pregunta y respuesta de seguridad 
$pregunta = "¿Cómo se llamaba tu primera mascota?"; 
$respuesta = "Lupo"; 

// Hash de la contraseña
$hash = password_hash($pass, PASSWORD_DEFAULT);

// Hash de la respuesta de seguridad 
$hashRespuesta = password_hash($respuesta, PASSWORD_DEFAULT);

// Crear administrador en la BD
$sql = "INSERT INTO admin (usuario, password_hash, pregunta_seguridad, respuesta_hash) 
        VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $usuario, $hash, $pregunta, $hashRespuesta);
$stmt->execute();

echo "Administrador creado correctamente.";

