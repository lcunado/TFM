let CONFIG = null;

// Función que carga la configuración desde PHP
export async function cargarConfig() {
    if (CONFIG !== null) {
        return CONFIG; // si ya está cargado, no vuelve a hacer fetch
    }

    const respuesta = await fetch("/php/get-config.php");
    CONFIG = await respuesta.json();
    return CONFIG;
}
