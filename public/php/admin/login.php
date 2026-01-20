<?php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../private/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    // Buscar usuario
    $sql = "SELECT * FROM admin WHERE usuario = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $admin = $resultado->fetch_assoc();

    if ($admin) {

        // Comprobar si está bloqueado
        if ($admin['bloqueado_hasta'] !== null && strtotime($admin['bloqueado_hasta']) > time()) {
            $minutos = ceil((strtotime($admin['bloqueado_hasta']) - time()) / 60);
            $error = "Demasiados intentos fallidos. Inténtalo de nuevo en $minutos minutos.";
        } else {

            // Verificar contraseña
            if (password_verify($pass, $admin['password_hash'])) {

                // Reiniciar intentos
                $sql = "UPDATE admin SET intentos = 0, bloqueado_hasta = NULL WHERE id = ?";
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

                    $sql = "UPDATE admin SET intentos = ?, bloqueado_hasta = ? WHERE id = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("isi", $intentos, $bloqueo, $admin['id']);
                    $stmt->execute();

                    $error = "Has superado el número de intentos. Cuenta bloqueada durante 10 minutos.";
                } else {
                    // Solo aumentar intentos
                    $sql = "UPDATE admin SET intentos = ? WHERE id = ?";
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

<form method="POST">
    <input type="text" name="user" placeholder="Usuario" required>
    <input type="password" name="pass" placeholder="Contraseña" required>
    <button type="submit">Entrar</button>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
</form>





