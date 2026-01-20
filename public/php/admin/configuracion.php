<?php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
session_start();

// Si no hay sesión, redirige al login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Cargar configuración actual
require_once __DIR__ . '/../../private/config.php';

// Obtener configuración
$sql = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
$result = $conexion->query($sql);
$config = $result->fetch_assoc();

// Decodificar JSON
$config['informacionGeneral'] = json_decode($config['informacionGeneral'], true);
$config['iconosIncluidos'] = json_decode($config['iconosIncluidos'], true);
$config['galeria'] = json_decode($config['galeria'], true);
$config['lugaresInteres'] = json_decode($config['lugaresInteres'], true) ?? [];
$config['politicasReserva'] = json_decode($config['politicasReserva'], true) ?? [];

// Título de la página
$title = "Editar configuración";

// Empieza a capturar el contenido
ob_start();
?>

    <h1 class="admin-section__title">Editar información del piso</h1>

    <?php if (isset($_GET['ok'])): ?>
        <div class="ok">Los cambios se han guardado correctamente.</div>
    <?php endif; ?>

    <form class="form" action="actualizar-config.php" method="POST">

        <!-- ============================
            DATOS DE LA VIVIENDA (EDITOR VISUAL)
        ============================= -->
        <div class="admin-section">
            <h2 class="admin-section__title">Datos de la vivienda</h2>
            
            <label class="form__label">Título de presentación</label> 
            <input class="form__input" type="text" name="titulo" value="<?= htmlspecialchars($config['titulo']) ?>">

            <label class="form__label">Nombre de la vivienda</label>
            <input class="form__input" type="text" name="vivienda" value="<?= htmlspecialchars($config['vivienda']) ?>">

            <label class="form__label">Imagen de fondo (ruta)</label>
            <input class="form__input" type="text" name="imagenFondo" value="<?= htmlspecialchars($config['imagenFondo']) ?>">

            <label class="form__label">Calle y número</label>
            <input class="form__input" type="text" name="direccionCalle" value="<?= htmlspecialchars($config['direccionCalle']) ?>">

            <label class="form__label">Código postal</label>
            <input class="form__input" type="text" name="direccionCP" value="<?= htmlspecialchars($config['direccionCP']) ?>">

            <label class="form__label">Ciudad</label>
            <input class="form__input" type="text" name="direccionCiudad" value="<?= htmlspecialchars($config['direccionCiudad']) ?>">

            <label class="form__label">País</label>
            <input class="form__input" type="text" name="direccionPais" value="<?= htmlspecialchars($config['direccionPais']) ?>">

            <label class="form__label">Latitud</label>
            <input class="form__input" type="text" name="latitud" value="<?= htmlspecialchars($config['latitud']) ?>">

            <label class="form__label">Longitud</label>
            <input class="form__input" type="text" name="longitud" value="<?= htmlspecialchars($config['longitud']) ?>">
        
            <label class="form__label">Precio por noche (€)</label> 
            <input class="form__input" type="number" step="0.01" name="precioDiario" value="<?= htmlspecialchars($config['precioDiario']) ?>">

        </div>

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
            LUGARES DE INTERÉS (EDITOR VISUAL)
        ============================= -->
        <div class="admin-section">
            <h2 class="admin-section__title">Lugares de interés</h2>

            <div id="lugaresContainer" class="editor-list"></div>

            <button type="button" class="button" onclick="addLugar()">
                Añadir lugar
            </button>

            <input type="hidden" name="lugaresInteres" id="lugaresInteresInput">
        </div>


        <!-- ============================
             ICONOS INCLUIDOS (EDITOR VISUAL)
        ============================= -->
        <div class="admin-section">
            <h2 class="admin-section__title">Iconos incluidos</h2>

            <label>Metros cuadrados</label>
            <input id="metrosCuadradosInput" class="form__input" type="number" name="metrosCuadrados" value="<?= $config['metrosCuadrados'] ?>">

            <label>Número máximo de huéspedes</label>
            <input id="maxHuespedesInput" class="form__input" type="number" name="maxHuespedes" value="<?= $config['maxHuespedes'] ?>">

            <label>Número de habitaciones</label>
            <input id="numHabitacionesInput" class="form__input" type="number" name="numHabitaciones" value="<?= $config['numHabitaciones'] ?>">

            <label>Número de baños</label>
            <input id="numBanosInput" class="form__input" type="number" name="numBanos" value="<?= $config['numBanos'] ?>">

            <label>Edad bebés gratis</label>
            <input id="edadBebesGratisInput" class="form__input" type="number" name="edadBebesGratis" value="<?= $config['edadBebesGratis'] ?>">

            <label><input id="iconoGaraje" type="checkbox" name="iconoGaraje" <?= $config['iconoGaraje'] ? 'checked' : '' ?>> Garaje</label>
            <label><input id="iconoMascotas" type="checkbox" name="iconoMascotas" <?= $config['iconoMascotas'] ? 'checked' : '' ?>> Admite mascotas</label>
            <label><input id="iconoChimenea" type="checkbox" name="iconoChimenea" <?= $config['iconoChimenea'] ? 'checked' : '' ?>> Chimenea</label>
            <label><input id="iconoBarbacoa" type="checkbox" name="iconoBarbacoa" <?= $config['iconoBarbacoa'] ? 'checked' : '' ?>> Barbacoa</label>
            <label><input id="iconoJardin" type="checkbox" name="iconoJardin" <?= $config['iconoJardin'] ? 'checked' : '' ?>> Jardín</label>
            <label><input id="iconoWifi" type="checkbox" name="iconoWifi" <?= $config['iconoWifi'] ? 'checked' : '' ?>> Wi‑Fi</label>
            <label><input id="iconoEquipado" type="checkbox" name="iconoEquipado" <?= $config['iconoEquipado'] ? 'checked' : '' ?>> Totalmente equipado</label>
            <label><input id="iconoCalefaccion" type="checkbox" name="iconoCalefaccion" <?= $config['iconoCalefaccion'] ? 'checked' : '' ?>> Calefacción</label>

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
    // Datos generales
    const titulo = <?= json_encode($config['titulo'], JSON_UNESCAPED_UNICODE) ?>;
    const imagenFondo = <?= json_encode($config['imagenFondo'], JSON_UNESCAPED_UNICODE) ?>;

    const direccionCalle = <?= json_encode($config['direccionCalle'], JSON_UNESCAPED_UNICODE) ?>;
    const direccionCP = <?= json_encode($config['direccionCP'], JSON_UNESCAPED_UNICODE) ?>;
    const direccionCiudad = <?= json_encode($config['direccionCiudad'], JSON_UNESCAPED_UNICODE) ?>;
    const direccionPais = <?= json_encode($config['direccionPais'], JSON_UNESCAPED_UNICODE) ?>;

    const horarioEntrada = <?= json_encode($config['horarioEntrada'], JSON_UNESCAPED_UNICODE) ?>;
    const horarioSalida = <?= json_encode($config['horarioSalida'], JSON_UNESCAPED_UNICODE) ?>;

    const precioDiario = <?= json_encode($config['precioDiario'], JSON_UNESCAPED_UNICODE) ?>;

    // Arrays
    const infoGeneral = <?= json_encode($config['informacionGeneral'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const lugaresInteres = <?= json_encode($config['lugaresInteres'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const politicasReserva = <?= json_encode($config['politicasReserva'] ?? [], JSON_UNESCAPED_UNICODE) ?>;

    // Datos numéricos
    const metrosCuadrados = <?= json_encode($config['metrosCuadrados']) ?>;
    const maxHuespedes = <?= json_encode($config['maxHuespedes']) ?>;
    const numHabitaciones = <?= json_encode($config['numHabitaciones']) ?>;
    const numBanos = <?= json_encode($config['numBanos']) ?>;
    const edadBebesGratis = <?= json_encode($config['edadBebesGratis']) ?>;

    // Checkboxes
    const iconoGaraje = <?= json_encode($config['iconoGaraje']) ?>;
    const iconoMascotas = <?= json_encode($config['iconoMascotas']) ?>;
    const iconoChimenea = <?= json_encode($config['iconoChimenea']) ?>;
    const iconoBarbacoa = <?= json_encode($config['iconoBarbacoa']) ?>;
    const iconoJardin = <?= json_encode($config['iconoJardin']) ?>;
    const iconoWifi = <?= json_encode($config['iconoWifi']) ?>;
    const iconoEquipado = <?= json_encode($config['iconoEquipado']) ?>;
    const iconoCalefaccion = <?= json_encode($config['iconoCalefaccion']) ?>;
</script>

<!-- Scripts -->
<script src="js/configuracion.js"></script>
<script src="/dist/admin.js"></script>

</body>
</html>



<?php

// Guardamos el contenido
$content = ob_get_clean();

// Cargamos el layout común
include __DIR__ . '/layout.php';







