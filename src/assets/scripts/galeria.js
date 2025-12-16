// Número máximo de fotos 
const totalFotos = 16; 
const galeria = document.getElementById("galeria");

// Array con títulos
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
  const figure = document.createElement("figure");
  figure.classList.add("galeria__item");

  const img = document.createElement("img");
  img.src = `/galeria/${String(i).padStart(2, "0")}.webp`;
  img.alt = captions[i-1] || `Imagen ${i}`;
  img.classList.add("galeria__image");

  const caption = document.createElement("figcaption");
  caption.classList.add("galeria__caption");
  caption.textContent = captions[i-1] || `Imagen ${i}`;

  figure.appendChild(img);
  figure.appendChild(caption);
  galeria.appendChild(figure);
}
