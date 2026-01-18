<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<h1>Panel de administración</h1>

<ul>
    <li><a href="configuracion.php">Editar información del piso</a></li>
    <li><a href="galeria.php">Gestionar galería</a></li>
    <li><a href="galeria.php">Gestionar reservas</a></li>
    <li><a href="galeria.php">Gestionar valoraciones</a></li>
    <li><a href="logout.php">Cerrar sesión</a></li>
</ul>
