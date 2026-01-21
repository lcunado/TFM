<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../private/config.php';

// Obtener reservas
$sql = "SELECT 
            id,
            dni,
            nombre,
            apellidos,
            email,
            telefono,
            num_personas,
            fecha_entrada,
            fecha_salida,
            precio,
            estado
        FROM reservas
        ORDER BY fecha_entrada DESC";

$result = $conexion->query($sql);

// Renderizar vista
$title = "Reservas";

ob_start();
?>

<h1>Listado de reservas</h1>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Personas</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Precio (€)</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['dni']) ?></td>
                    <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td><?= $row['num_personas'] ?></td>
                    <td><?= $row['fecha_entrada'] ?></td>
                    <td><?= $row['fecha_salida'] ?></td>
                    <td><?= number_format($row['precio'], 2) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';





