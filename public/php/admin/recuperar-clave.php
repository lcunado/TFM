<?php
session_start();
require_once __DIR__ . '/../../private/config.php';

// Enviar email con PHPMailer
require __DIR__ . '/../vendor/phpmailer/Exception.php';
require __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require __DIR__ . '/../vendor/phpmailer/SMTP.php';

// Envío de correos con PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user']); // Recoger usuario 

    // Buscar usuario
    $stmt = $conexion->prepare("SELECT id, email 
                                FROM admin WHERE usuario = ? 
                                LIMIT 1");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($idAdmin, $emailAdmin);
    $stmt->fetch();
    $stmt->close();

    if (!$idAdmin) {
        $error = "No existe ningún administrador con ese usuario.";
    } else {
        // Generar token seguro
        $token = bin2hex(random_bytes(32));
        $caduca = date("Y-m-d H:i:s", time() + 3600); // 1 hora

        // Guardar token en BD
        $stmt = $conexion->prepare("UPDATE admin 
                                    SET reset_token = ?, reset_caduca = ? 
                                    WHERE id = ?");
        $stmt->bind_param("ssi", $token, $caduca, $idAdmin);
        $stmt->execute();
        $stmt->close();
        
        // Datos de configuracion para el email
        $result = $conexion->query("SELECT email, dominio 
                                    FROM configuracion 
                                    WHERE id = 1");
        $CONFIG = $result->fetch_assoc();

        $emailPropietario   = $CONFIG['email'];  
        $dominio            = $CONFIG['dominio'];

        // Enviar email
        $mail = new PHPMailer(true);
        
        $resetLink = $dominio . "/php/admin/resetear-clave.php?codigo=" . $token;
    
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $emailPropietario;
            $mail->Password   = $propietarioPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remitente
            $mail->setFrom($emailPropietario, 'Recuperación de contraseña'); // Remitente
            
            // Correo
            $mail->addAddress($emailAdmin); 
            $mail->Subject = "Enlace de acceso seguro";
            $mail->isHTML(true);
            $mail->Body = "
                <html>
                    <p>Se ha solicitado un enlace de acceso seguro.</p>
                    <p>Haz clic aquí para continuar:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>Caduca en 1 hora.</p>
                </html>
            ";

            $mail->send();
            $ok = "Se ha enviado un enlace de recuperación a tu correo.";

        } catch (Exception $e) {
            $error = "Error al enviar el correo: " . $e->getMessage();
        }
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
                <input class="form__input" type="text" name="user" placeholder="Usuario" required>
                <button class="button" type="submit">Enviar enlace seguro</button>
            </form>
        <?php endif; ?>
        
    </div>
</body>
</html>
