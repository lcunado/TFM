<?php
session_start();
require_once __DIR__ . '/../../private/config.php';

// Función de contraseña segura 
function password_segura($pass) { 
    if (strlen($pass) < 8) return false; 
    if (!preg_match('/[A-Z]/', $pass)) return false; 
    if (!preg_match('/[a-z]/', $pass)) return false; 
    if (!preg_match('/[0-9]/', $pass)) return false; 
    if (!preg_match('/[\W_]/', $pass)) return false; 
    return true; 
}

$token = $_GET['codigo'] ?? '';

if (!$token) {
    die("Token inválido.");
}

// Buscar token
$stmt = $conexion->prepare("SELECT id, reset_caduca 
                            FROM admin 
                            WHERE reset_token = ? 
                            LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($idAdmin, $caduca);
$stmt->fetch();
$stmt->close();

if (!$idAdmin) {
    die("Token no válido.");
}

if (strtotime($caduca) < time()) {
    die("El enlace ha caducado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 !== $pass2) { 
        $error = "Las contraseñas no coinciden."; 
    } elseif (!password_segura($pass1)) { 
        $error = "La contraseña debe tener al menos 8 caracteres, mayúsculas, minúsculas, números y símbolos."; 
    } else { 
        $hash = password_hash($pass1, PASSWORD_DEFAULT); 

        $stmt = $conexion->prepare("UPDATE admin 
                                    SET password_hash = ?, reset_token = NULL, reset_caduca = NULL 
                                    WHERE id = ?"); 
        $stmt->bind_param("si", $hash, $idAdmin); 
        $stmt->execute(); 
        $stmt->close(); 

        $ok = "Contraseña actualizada correctamente. Ya puedes iniciar sesión."; 
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso seguro</title>
    <?php include __DIR__ . '/admin-styles.php'; ?>
</head>
<body>
    <div class="login-box"> 
        <h2 class="login-title">Acceso seguro</h2> 

        <?php if(isset($error)): ?> 
            <div class="error-box"><?= $error ?></div> 
        <?php endif; ?> 

        <?php if(isset($ok)): ?> 
            <div class="success-box"><?= $ok ?></div> 
        <?php endif; ?> 
        
        <?php if(!isset($ok)): ?> 
            <form method="POST"> 
                <input class="form__input" type="password" name="pass1" placeholder="Nueva contraseña" required> 
                <input class="form__input" type="password" name="pass2" placeholder="Repetir contraseña" required> 
                <button class="button" type="submit">Actualizar contraseña</button> 
            </form> 
        <?php endif; ?> 
    </div>
</body>
</html>
