// Número máximo de fotos 
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
