<?php
require_once __DIR__ . '/../private/config.php';

function password_segura($pass) {
    if (strlen($pass) < 8) return false;
    if (!preg_match('/[A-Z]/', $pass)) return false;
    if (!preg_match('/[a-z]/', $pass)) return false;
    if (!preg_match('/[0-9]/', $pass)) return false;
    if (!preg_match('/[\W_]/', $pass)) return false;
    return true;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = "admin";
    $email   = $_POST['email'];
    $passRaw = $_POST['password'];

    if (!password_segura($passRaw)) {
        die("La contraseña no cumple los requisitos de seguridad."); 
    }

    $pass    = password_hash($passRaw, PASSWORD_DEFAULT);

    // Insertar admin
    $stmt = $conexion->prepare("INSERT INTO admin (usuario, email, password_hash) 
                                VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $email, $pass);
    $stmt->execute();
    $stmt->close();

    header("Location: finalizar.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crear administrador</title>
</head>
<body>
    <h2>Crear administrador</h2>

    <form method="POST">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Contraseña</label>
        <input type="password" name="password" id="nueva" required> 
        
        <div id="seguridad-pass" style="margin: 10px 0; font-size: 14px;"></div>

        <button type="submit">Crear administrador</button>
    
    </form>

    <script src="validar-password.js"></script>

</body>

</html>
