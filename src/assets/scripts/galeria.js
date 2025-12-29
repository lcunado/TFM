// Número de fotos 
const totalFotos = 16; 

// Contenedor principal donde se insertarán las imágenes
const galeria = document.getElementById("galeria");

// Array con títulos para cada imagen (en orden)
const captions = [
  "Salón principal",
  "Habitación doble",
  "Habitación doble",
  "Habitación doble",
  "Habitación individual",
  "Habitación individual",
  "Cocina",
  "Cocina",
  "Baño 1",
  "Baño 1",
  "Baño 2",
  "Baño 2",
  "Terraza",
  "Fachada",
  "Vistas",
  "Garaje"
];

// Generar dinámicamente las figuras
for (let i = 1; i <= totalFotos; i++) {
  // Contenedor de cada imagen
  const figure = document.createElement("figure");
  figure.classList.add("galeria__item");
  // Imagen
  const img = document.createElement("img");
  img.src = `/galeria/${String(i).padStart(2, "0")}.webp`;
  img.alt = captions[i-1] || `Imagen ${i}`;
  img.classList.add("galeria__image");
  // Pie de foto
  const caption = document.createElement("figcaption");
  caption.classList.add("galeria__caption");
  caption.textContent = captions[i-1] || `Imagen ${i}`;
  // Insertar imagen y pie de foto dentro del contendor de la imagen
  figure.appendChild(img);
  figure.appendChild(caption);
  // Agregarlo al contenedor principal de la galería
  galeria.appendChild(figure);
}

// Mostrar la imagen en grande
// Obtenemos los elementos del HTML
const visor = document.getElementById("visor");
const visorImg = document.getElementById("visor-img");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");

// Guardamos todas las imágenes en un array
const imagenes = Array.from(document.querySelectorAll(".galeria__image"));
let indiceActual = 0;

// Función para mostrar una imagen en el visor
function mostrarImagen(index) {
  indiceActual = index;
  visorImg.src = imagenes[indiceActual].src;
  visor.classList.remove("hidden");
}

// Abrir visor al clicar una imagen 
galeria.addEventListener("click", (e) => {
  if (e.target.tagName === "IMG") {
    const index = imagenes.indexOf(e.target);
    mostrarImagen(index);
  }
});

// Cerrar visor al hacer clic fuera
visor.addEventListener("click", (e) => {
  if (e.target === visor) {
    visor.classList.add("hidden");
  }
});

// Navegar a la imagen anterior
prevBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
  mostrarImagen(indiceActual);
});

// Navegar a la imagen siguiente
nextBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  indiceActual = (indiceActual + 1) % imagenes.length;
  mostrarImagen(indiceActual);
});

// Navegación con teclado
document.addEventListener("keydown", (e) => {
  if (visor.classList.contains("hidden")) return;

  if (e.key === "ArrowLeft") {
    indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
    mostrarImagen(indiceActual);
  }

  if (e.key === "ArrowRight") {
    indiceActual = (indiceActual + 1) % imagenes.length;
    mostrarImagen(indiceActual);
  }

  if (e.key === "Escape") {
    visor.classList.add("hidden");
  }
});
