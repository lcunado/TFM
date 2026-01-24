<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$title = "Dashboard";

// Empieza a capturar el contenido
ob_start();
?>

<h1 class="admin-section__title">Bienvenido admin</h1>

<div class="admin-section">
    <p>Este es tu panel de control.</p>
</div>

<?php
// Guardar el contenido en $content
$content = ob_get_clean();

// Cargar el layout
include __DIR__ . '/layout.php';


