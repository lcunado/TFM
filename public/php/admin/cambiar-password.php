<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$title = "Cambiar contraseña";

ob_start();
?>

<h1 class="admin-section__title">Cambiar contraseña</h1>
<div class="form">

    <?php if (isset($_GET['error'])): ?>
        <div class="admin-alert admin-alert--error">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['ok'])): ?>
        <div class="admin-alert admin-alert--success">Contraseña actualizada correctamente.</div>
    <?php endif; ?>

    <div class="admin-section">
        <form class="form" action="cambiar-password-guardar.php" method="POST">

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
</div>

<script src="js/validar-password.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';




