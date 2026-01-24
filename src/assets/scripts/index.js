import { cargarConfig } from "./config.js";

import { cargarFooter } from "./footer.js";

document.addEventListener("DOMContentLoaded", cargarFooter);

async function init() {
    const CONFIG = await cargarConfig();

    // Convertir iconos "0"/"1" en booleanos reales 
    [ "iconoGaraje", "iconoMascotas", "iconoChimenea", "iconoBarbacoa", "iconoJardin", "iconoWifi", "iconoEquipado", "iconoCalefaccion" ].forEach(key => { 
        CONFIG[key] = CONFIG[key] == 1; // Devuelve true o false
    });

    /* ------------------------------
       CABECERA PRINCIPAL
    ------------------------------ */

    document.querySelector(".titulo").textContent = CONFIG.titulo;
    document.querySelectorAll(".vivienda").forEach(el => el.textContent = CONFIG.vivienda);
    document.querySelector(".index__image").src = "/assets/images/" + CONFIG.imagenFondo;
    document.querySelector(".bloque__comment").textContent =
        `Desde ${parseInt(CONFIG.precioDiario)}€ la noche`;
    
    /* ------------------------------
        SECCIÓN INFORMACIÓN GENERAL
    ------------------------------ */

    const textosContainer = document.querySelector(".info__textos");
    textosContainer.innerHTML = "";
    CONFIG.informacionGeneral.forEach(texto => {
        textosContainer.innerHTML += `<p>${texto}</p>`;
    });

    /* ------------------------------
        SECCIÓN LUGARES DE INTERÉS
    ------------------------------ */

    const lugaresContainer = document.querySelector(".info__lugares");
    lugaresContainer.innerHTML = "";
    CONFIG.lugaresInteres.forEach(lugar => {
        lugaresContainer.innerHTML += `<li>${lugar}</li>`;
    });

    /* ------------------------------
       SECCIÓN ICONOS INCLUIDOS
    ------------------------------ */

    const iconosContainer = document.querySelector(".icon__grid");
    iconosContainer.innerHTML = "";

    const iconos = [];

    // Iconos con texto dinámico
    iconos.push({
        icono: "fa-house-user",
        texto: `${CONFIG.metrosCuadrados} m²`
    });

    iconos.push({
        icono: "fa-users",
        texto: `1-${CONFIG.maxHuespedes} personas`
    });

    iconos.push({
        icono: "fa-bed",
        texto: `${CONFIG.numHabitaciones} habitaciones`
    });

    iconos.push({
        icono: "fa-bath",
        texto: `${CONFIG.numBanos} baños`
    });

    iconos.push({
        icono: "fa-baby",
        texto: `Niños 0-${CONFIG.edadBebesGratis} años gratis`
    });

    // Iconos activables (sí/no)
    if (CONFIG.iconoGaraje)
        iconos.push({ icono: "fa-square-parking", texto: "Garaje" });

    if (CONFIG.iconoMascotas)
        iconos.push({ icono: "fa-paw", texto: "Admite mascotas" });

    if (CONFIG.iconoChimenea)
        iconos.push({ icono: "fa-fire", texto: "Chimenea" });

    if (CONFIG.iconoBarbacoa)
        iconos.push({ icono: "fa-drumstick-bite", texto: "Barbacoa" });

    if (CONFIG.iconoJardin)
        iconos.push({ icono: "fa-tree", texto: "Jardín" });

    if (CONFIG.iconoWifi)
        iconos.push({ icono: "fa-wifi", texto: "Wi‑Fi gratuito" });

    if (CONFIG.iconoEquipado)
        iconos.push({ icono: "fa-question", texto: "Totalmente equipado" });

    if (CONFIG.iconoCalefaccion)
        iconos.push({ icono: "fa-temperature-high", texto: "Calefacción" });

    // Pintar iconos
    iconos.forEach(item => {
        iconosContainer.innerHTML += `
            <div class="icon__item">
                <i class="fa ${item.icono}"></i>
                <span>${item.texto}</span>
            </div>
        `;
    });


    /* ------------------------------
       POLÍTICAS DE RESERVA
    ------------------------------ */
    document.querySelector(".politicas__entrada").innerHTML = ` 
        <div class="icon__item">
            <i class="fa fa-clock"></i>
            <span>Entrada a partir de las ${CONFIG.horarioEntrada}</span>
        </div>
    `; 
    document.querySelector(".politicas__salida").innerHTML = ` 
        <div class="icon__item">
            <i class="fa fa-clock"></i>
            <span>Salida hasta las ${CONFIG.horarioSalida}</span>
        </div>    
    `;

    const politicasTextos = document.querySelector(".politicas__textos");
    politicasTextos.innerHTML = "";
    CONFIG.politicasReserva.forEach(texto => {
        politicasTextos.innerHTML += `<p>${texto}</p>`;
    });
    const dias = CONFIG.diasReembolsoCompleto;
    const porcentaje = CONFIG.porcentajeReembolso * 100;

    const fraseCancelacion = `
    <p class="politica-cancelacion">
        Política de cancelación: se reembolsa el 100% del importe pagado si se cancela con al menos 
        ${dias} días de antelación. En cancelaciones posteriores, se devuelve el ${porcentaje}%.
    </p>
    `;
    // Añadir al final del contenido existente
    document.querySelector(".politicas__textos").innerHTML += fraseCancelacion;


    /* ------------------------------
       MAPA
    ------------------------------ */

    const mapa = document.querySelector(".mapa");
    if (mapa) {
        mapa.src = `https://maps.google.com/maps?q=${CONFIG.latitud},${CONFIG.longitud}&z=15&output=embed`;
    }

}

init();

