<?php
require_once "config.php";

$todas = isset($_GET['todas']) && $_GET['todas'] === "true";

// Consulta: 3 últimas o todas
if ($todas) {
    $sql = "SELECT * FROM valoraciones ORDER BY fecha_valoracion DESC";
} else {
    $sql = "SELECT * FROM valoraciones ORDER BY fecha_valoracion DESC LIMIT 3";
}

$result = $conexion->query($sql);

// Calcular total y media general
$totalRes = $conexion->query("SELECT COUNT(*) AS c, AVG(general) AS media FROM valoraciones");
$stats = $totalRes->fetch_assoc();
$total = (int)$stats["c"];
$media = $stats["media"] ? round($stats["media"], 2) : 0;

echo "<h2>Valoraciones de nuestros huéspedes - ⭐ $media/5 ($total valoraciones)</h2>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="valoraciones__item">';
        echo '<h3 class="valoraciones__title">' . htmlspecialchars($row['nombre']) . 
             ' - ⭐ ' . (int)$row['general'] . '/5 <small class="valoraciones__date">' . 
             date("d/m/Y", strtotime($row['fecha_valoracion'])) . '</small></h3>';
        echo '<p class="valoraciones__detail"><strong>Limpieza:</strong> ' . (int)$row['limpieza'] . '/5 | ';
        echo '<strong>Veracidad:</strong> ' . (int)$row['veracidad'] . '/5</p>';
        echo '<p class="valoraciones__detail"><strong>Llegada:</strong> ' . (int)$row['llegada'] . '/5 | ';
        echo '<strong>Comunicación:</strong> ' . (int)$row['comunicacion'] . '/5</p>';
        echo '<p class="valoraciones__detail"><strong>Ubicación:</strong> ' . (int)$row['ubicacion'] . '/5 | ';
        echo '<strong>Calidad:</strong> ' . (int)$row['calidad'] . '/5</p>';
        if (!empty($row['comentario'])) {
            echo '<p class="valoraciones__comment">"' . nl2br(htmlspecialchars($row['comentario'])) . '"</p>';
        }
        echo '</div>';
    }
} else {
    echo '<p class="valoraciones__empty">No hay valoraciones registradas todavía.</p>';
}

// Mostrar botón solo si hay más de 3 y no se pidieron todas
if ($total > 3 && !$todas) {
    echo '<div class="button__wrapper">
            <button type="submit" class="button" id="mostrar-todas">Mostrar todas las valoraciones</button>
          </div>';
}

$conexion->close();
?>
