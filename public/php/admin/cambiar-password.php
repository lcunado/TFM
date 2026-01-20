<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$title = "Cambiar contraseña";

ob_start();
?>

<div class="form">
    <h2 class="form__title">Cambiar contraseña</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="ok" style="background:#f8d7da;color:#721c24;border-color:#f5c6cb;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['ok'])): ?>
        <div class="ok">Contraseña actualizada correctamente.</div>
    <?php endif; ?>

    <form method="POST" action="cambiar-password-guardar.php">

        <label class="form__label">Contraseña actual</label> 
        <input class="form__input" type="password" name="actual" required> 
        
        <label class="form__label">Nueva contraseña</label> 
        <input class="form__input" type="password" name="nueva" id="nueva" required> 
        <div id="seguridad-pass" class="form__label"></div> 
        
        <label class="form__label">Repetir nueva contraseña</label> 
        <input class="form__input" type="password" name="repetir" required> 
        
        <button class="button" type="submit">Actualizar contraseña</button>
    </form>
</div>

<script src="js/validar-password.js"></script>


<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';




