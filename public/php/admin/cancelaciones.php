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
            estado_cancelacion,
            fecha_reembolso
        FROM cancelaciones
        ORDER BY fecha_cancelacion DESC";

$result = $conexion->query($sql);

// Renderizar vista
$title = "Cancelaciones";

ob_start();
?>

<h1>Listado de cancelaciones</h1>

<?php if (isset($_GET['refund']) && $_GET['refund'] === 'ok'): ?> 
    <div class="admin-alert admin-alert--success"> 
        ✔ La devolución se ha realizado correctamente. 
    </div> 
<?php endif; ?>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Cancelación</th>
                <th>ID Reserva</th>
                <th>Fecha cancelación</th>
                <th>Pagado (€)</th>
                <th>Reembolsar (€)</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Fecha reembolso</th>
                <th>Acciones</th>
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
                    <td><?= $row['fecha_reembolso'] ?></td>
                    <td>
                        <?php if ($row['estado_cancelacion'] === 'pendiente'): ?>
                            <form action="devolucion-sesion.php" method="POST" class="inline-form">
                                <input type="hidden" name="id_cancelacion" value="<?= $row['id_cancelacion'] ?>">
                                <input type="hidden" name="id_reserva" value="<?= $row['id_reserva'] ?>">
                                <?php $importe = number_format($row['importe_reembolsar'], 2); ?> 
                                <button class="button button--danger" 
                                    onclick="return confirm('¿Seguro que deseas devolver <?= $importe ?> €?')"> 
                                    Realizar devolución 
                                </button>
                            </form>

                        <?php else: ?>
                            <span class="estado-completada">Completada</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';






