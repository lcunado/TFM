<?php
// layout.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Panel Admin' ?></title>

    <?php include __DIR__ . '/admin-styles.php'; ?>
</head>

<body>

    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="configuracion.php">Configuración</a></li>
            <li><a href="galeria.php">Galería</a></li>
            <li><a href="reservas.php">Reservas</a></li>
            <li><a href="cambiar-password.php">Cambiar contraseña</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
            <li><a href="/index.html">Volver a la web</a></li>
        </ul>
    </nav>

    <div class="admin-panel">
        <?= $content ?>
    </div>

</body>
</html>



