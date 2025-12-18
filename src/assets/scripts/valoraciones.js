import leoProfanity from "leo-profanity";

// cargar diccionario español
leoProfanity.loadDictionary("es");

// añadir lista personalizada
leoProfanity.add(["mierda", "joder", "puta", "cabron", "coño"]);

// prueba rápida
console.log(leoProfanity.check("esto es una mierda")); 
// → true si la palabra está en el diccionario, false si no

console.log(leoProfanity.clean("esto es una mierda")); 
// → "esto es una *****"

document.getElementById("valoracion-form").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    
    // Filtrar comentario
    let comentario = formData.get("comentario");
    if (comentario && leoProfanity.check(comentario)) {
        formData.set("comentario", leoProfanity.clean(comentario));
    }
    
    fetch("insert-valoracion.php", {
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

function cargarValoraciones(mostrarTodas = false) {
    let url = mostrarTodas ? "get-valoraciones.php?todas=true" : "get-valoraciones.php";

    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Sustituir el contenido del bloque por el HTML que devuelve PHP
            document.getElementById("valoraciones-container").innerHTML = html;

            // Si existe el botón "Mostrar todas", añadir el event listener
            const botonMostrarTodas = document.getElementById("mostrar-todas");
            if (botonMostrarTodas) {
                botonMostrarTodas.addEventListener("click", () => cargarValoraciones(true));
            }
        })
        .catch(error => console.error("⚠️ Error al cargar valoraciones:", error));
}

// Cargar solo 3 valoraciones al iniciar
document.addEventListener("DOMContentLoaded", () => cargarValoraciones(false));
