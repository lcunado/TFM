<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../private/config.php';

// Obtener valoraciones
$sql = "SELECT 
            id,
            nombre,
            general,
            limpieza,
            veracidad,
            llegada,
            comunicacion,
            ubicacion,
            calidad,
            comentario,
            fecha_valoracion
        FROM valoraciones
        ORDER BY fecha_valoracion DESC";

$result = $conexion->query($sql);

// Renderizar vista
$title = "Valoraciones";

ob_start();
?>

<h1 class="admin-section__title">Listado de valoraciones</h1>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>General</th>
                <th>Limpieza</th>
                <th>Veracidad</th>
                <th>Llegada</th>
                <th>Comunicación</th>
                <th>Ubicación</th>
                <th>Calidad</th>
                <th>Comentario</th>
                <th>Fecha</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= $row['general'] ?></td>
                    <td><?= $row['limpieza'] ?></td>
                    <td><?= $row['veracidad'] ?></td>
                    <td><?= $row['llegada'] ?></td>
                    <td><?= $row['comunicacion'] ?></td>
                    <td><?= $row['ubicacion'] ?></td>
                    <td><?= $row['calidad'] ?></td>
                    <td><?= nl2br(htmlspecialchars($row['comentario'])) ?></td>
                    <td><?= $row['fecha_valoracion'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';






