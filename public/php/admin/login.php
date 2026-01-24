<?php
session_start();

// Conexión BD
require_once __DIR__ . '/../../private/config.php';

// Si el formulario se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user']; // Recoger user
    $pass = $_POST['pass']; // Recoger contraseña

    // Buscar usuario
    $sql = "SELECT * FROM admin 
            WHERE usuario = ? 
            LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $admin = $resultado->fetch_assoc();

    if ($admin) {
        // Comprobar si está bloqueado
        if ($admin['bloqueado_hasta'] !== null && strtotime($admin['bloqueado_hasta']) > time()) {
            $minutos = ceil((strtotime($admin['bloqueado_hasta']) - time()) / 60); // Segundos / minutos
            $error = "Demasiados intentos fallidos. Inténtalo de nuevo en $minutos minutos.";
        } else {
            // Verificar contraseña
            if (password_verify($pass, $admin['password_hash'])) {
                // Reiniciar intentos
                $sql = "UPDATE admin 
                        SET intentos = 0, bloqueado_hasta = NULL 
                        WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $admin['id']);
                $stmt->execute();

                $_SESSION['admin'] = true;
                header("Location: dashboard.php");
                exit;

            } else {
                // Incrementar intentos
                $intentos = $admin['intentos'] + 1;

                if ($intentos >= 5) {
                    // Bloquear 10 minutos
                    $bloqueo = date("Y-m-d H:i:s", time() + 10 * 60);

                    $sql = "UPDATE admin 
                            SET intentos = ?, bloqueado_hasta = ? 
                            WHERE id = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("isi", $intentos, $bloqueo, $admin['id']);
                    $stmt->execute();

                    $error = "Has superado el número de intentos. Cuenta bloqueada durante 10 minutos.";
                } else {
                    // Solo aumentar intentos
                    $sql = "UPDATE admin 
                            SET intentos = ? 
                            WHERE id = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ii", $intentos, $admin['id']);
                    $stmt->execute();

                    $error = "Credenciales incorrectas. Intento $intentos de 5.";
                }
            }
        }

    } else {
        $error = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html> 
<html lang="es"> 
    <head> 
        <meta charset="UTF-8"> 
        <title>Acceso Administrador</title> 
        <?php include __DIR__ . '/admin-styles.php'; ?>
    </head> 
    <body>
        <div class="login-box">
            <h2 class="login-title">Acceso Administrador</h2>

            <?php if(isset($error)): ?>
                <div class="admin-alert admin-alert--error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <input class="form__input" type="text" name="user" placeholder="Usuario" required>
                <input class="form__input" type="password" name="pass" placeholder="Contraseña" required>
                <button class="button" type="submit">Entrar</button>
            </form>
            <div class="link-container">
                <a href="recuperar-clave.php" class="link">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

        </div>
    </body>
</html>







