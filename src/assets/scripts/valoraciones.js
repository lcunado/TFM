import leoProfanity from "leo-profanity";
import { cargarFooter } from "./footer.js";

document.addEventListener("DOMContentLoaded", cargarFooter);

// Cargar diccionario español
leoProfanity.loadDictionary("es");

// Añadir lista personalizada
leoProfanity.add(["mierda", "joder", "puta", "cabron", "coño", "gilipollas"]);

// Guardar tiempo de inicio
let inicio = Date.now();

// Esperar a que el DOM esté listo
document.addEventListener("DOMContentLoaded", async () => { 
    // Cargar solo 3 valoraciones al iniciar 
    cargarValoraciones(false); 
});

// Obtener el contenedor
document.getElementById("valoracion-form").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this); // Datos recibidos

    // Validaciones
    // Nombre
    const nombre = formData.get("nombre")?.trim();
    if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/.test(nombre)) {
        alert("⚠️ El nombre no es válido.");
        return;
    }

    // Comentario
    const comentario = formData.get("comentario")?.trim();
    if (!comentario || comentario.length < 5 || comentario.length > 500) {
        alert("⚠️ El comentario debe tener entre 5 y 500 caracteres.");
        return;
    }

    // General
    const general = formData.get("general");
    if (!/^[1-5]$/.test(general)) {
        alert("⚠️ Debes seleccionar una valoración general.");
        return;
    }
    
    // Control Honeypot, si el campo oculto tiene contenido es spam
    if (formData.get("hp_field_valoraciones")) {
        alert("⚠️ Detección de spam. Comentario bloqueado.");
        return;
    }

    // Control tiempo, si tarda menos de 3 segundos es sospechoso
    let tiempo = Date.now() - inicio;
    if (tiempo < 3000) {
        alert("⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.");
        return;
    }

    // Filtrar comentario
    if (comentario && leoProfanity.check(comentario)) {
        formData.set("comentario", leoProfanity.clean(comentario));
    }
    
    // Envío al servidor
    fetch("/php/insert-valoracion.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Mostrar el mensaje que devuelve PHP directamente
        document.getElementById("valoracion-mensaje").innerHTML = data;

        // Refrescar lista de valoraciones
        cargarValoraciones();

        // Vaciar formulario
        this.reset();
    })
    .catch(error => console.error("⚠️ Error:", error));
});

// Función para cargar valoraciones
function cargarValoraciones(mostrarTodas = false) {
    // Si mostarTodas es true, se cargan todas
    let url = mostrarTodas ? "/php/get-valoraciones.php?todas=true" : "/php/get-valoraciones.php";

    fetch(url) // Llama a php
        .then(response => response.text())
        .then(html => {
            // Insertar el HTML generado por PHP
            document.getElementById("valoraciones-container").innerHTML = html;

            // Si existe el botón "Mostrar todas", añadir el event listener
            const botonMostrarTodas = document.getElementById("mostrar-todas");
            if (botonMostrarTodas) {
                botonMostrarTodas.addEventListener("click", () => cargarValoraciones(true));
            }
        })
        .catch(error => console.error("⚠️ Error al cargar valoraciones:", error));
}

