<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../private/config.php';

// Obtener cancelaciones
$sql = "SELECT 
            id_cancelacion,
            id_reserva,
            fecha_cancelacion,
            importe_pagado,
            importe_reembolsar,
            motivo,
            estado_cancelacion
        FROM cancelaciones
        ORDER BY fecha_cancelacion DESC";

$result = $conexion->query($sql);

// Renderizar vista
$title = "Cancelaciones";

ob_start();
?>

<h1>Listado de cancelaciones</h1>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Cancelación</th>
                <th>ID Reserva</th>
                <th>Fecha</th>
                <th>Pagado (€)</th>
                <th>Reembolsar (€)</th>
                <th>Motivo</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_cancelacion'] ?></td>
                    <td><?= $row['id_reserva'] ?></td>
                    <td><?= $row['fecha_cancelacion'] ?></td>
                    <td><?= number_format($row['importe_pagado'], 2) ?></td>
                    <td><?= number_format($row['importe_reembolsar'], 2) ?></td>
                    <td><?= htmlspecialchars($row['motivo']) ?></td>
                    <td><?= htmlspecialchars($row['estado_cancelacion']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';






