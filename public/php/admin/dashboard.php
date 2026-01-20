<?php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

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
// Guardamos el contenido en $content
$content = ob_get_clean();

// Cargamos el layout
include __DIR__ . '/layout.php';


