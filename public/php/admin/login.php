<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    if ($user === 'admin' && $pass === '1234') {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    }

    $error = "Credenciales incorrectas";
}
?>

<form method="POST">
    <input type="text" name="user" placeholder="Usuario">
    <input type="password" name="pass" placeholder="Contraseña">
    <button type="submit">Entrar</button>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
</form>


