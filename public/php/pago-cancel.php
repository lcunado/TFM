<!DOCTYPE html> 
<html lang="es"> 
    <head> 
        <meta charset="UTF-8"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <title>Pago cancelado</title> 
        <?php include __DIR__ . '/admin/admin-styles.php'; ?>
    </head> 
    <body>
        <div class="admin-panel">
            <div class="admin-section">

                <h1 class="admin-section__title">Pago cancelado</h1>

                <div class="icon__item icon__item--error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>La operación fue cancelada correctamente</span>
                </div>

                <p>No se ha realizado ningún cargo en tu tarjeta.</p>

                <a href="/reservas.html" class="button">Vuelve a intentarlo</a>

            </div>
        </div>

    </body>
</html>
