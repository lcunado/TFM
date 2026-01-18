<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config.php";

// Obtener configuración
$sql = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
$result = $conexion->query($sql);
$config = $result->fetch_assoc();

// Decodificar JSON
$config['informacionGeneral'] = json_decode($config['informacionGeneral'], true);
$config['iconosIncluidos'] = json_decode($config['iconosIncluidos'], true);
$config['politicasReserva'] = json_decode($config['politicasReserva'], true);
$config['galeria'] = json_decode($config['galeria'], true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar configuración</title>

    <!-- CSS compilado por Parcel -->
    <link rel="stylesheet" href="/dist/admin.css">
</head>
<body>

<div class="admin-panel">

    <h1 class="admin-section__title">Editar información del piso</h1>

    <?php if (isset($_GET['ok'])): ?>
        <div class="ok">Cambios guardados correctamente.</div>
    <?php endif; ?>

    <form class="form" action="actualizar-config.php" method="POST">

        <label class="form__label">Localidad</label>
        <input class="form__input" type="text" name="localidad" value="<?= htmlspecialchars($config['localidad']) ?>">

        <label class="form__label">Vivienda</label>
        <input class="form__input" type="text" name="vivienda" value="<?= htmlspecialchars($config['vivienda']) ?>">

        <label class="form__label">Dirección</label>
        <textarea class="form__textarea" name="direccion"><?= htmlspecialchars($config['direccion']) ?></textarea>

        <label class="form__label">Latitud</label>
        <input class="form__input" type="text" name="latitud" value="<?= htmlspecialchars($config['latitud']) ?>">

        <label class="form__label">Longitud</label>
        <input class="form__input" type="text" name="longitud" value="<?= htmlspecialchars($config['longitud']) ?>">

        <!-- ============================
             INFORMACIÓN GENERAL (EDITOR VISUAL)
        ============================= -->
        <div class="admin-section">
            <h2 class="admin-section__title">Información general</h2>

            <div id="infoGeneralContainer" class="editor-list"></div>

            <button type="button" class="button" onclick="addInfoGeneral()">
                Añadir elemento
            </button>

            <input type="hidden" name="informacionGeneral" id="informacionGeneralInput">
        </div>

        <!-- ============================
             ICONOS INCLUIDOS (EDITOR VISUAL)
        ============================= -->
        <div class="admin-section">
            <h2 class="admin-section__title">Iconos incluidos</h2>

            <div id="iconosContainer" class="editor-list"></div>

            <button type="button" class="button" onclick="addIcono()">
                Añadir icono
            </button>

            <input type="hidden" name="iconosIncluidos" id="iconosIncluidosInput">
        </div>

        <!-- ============================
             POLÍTICAS DE RESERVA 
        ============================= -->
        <div class="admin-section">
            <h2 class="admin-section__title">Políticas de reserva</h2>

            <div id="politicasContainer" class="editor-list"></div>

            <button type="button" class="button" onclick="addPolitica()">
                Añadir política
            </button>

            <input type="hidden" name="politicasReserva" id="politicasReservaInput">
        </div>


        <button class="button" type="submit">Guardar cambios</button>

    </form>
</div>

<!-- Inyección de datos desde PHP hacia JS -->
<script>
    const infoGeneral = <?= json_encode($config['informacionGeneral'], JSON_UNESCAPED_UNICODE) ?>;
    const iconosIncluidos = <?= json_encode($config['iconosIncluidos'], JSON_UNESCAPED_UNICODE) ?>;
    const politicasReserva = <?= json_encode($config['politicasReserva'], JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Script externo -->
<script src="js/configuracion.js"></script>

</body>
</html>


