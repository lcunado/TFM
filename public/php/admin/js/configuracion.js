/* ============================================================
   EDITOR VISUAL: INFORMACIÓN GENERAL
============================================================ */
function renderInfoGeneral() {
    const container = document.getElementById("infoGeneralContainer");
    container.innerHTML = "";

    infoGeneral.forEach((item, index) => {
        const div = document.createElement("div");
        div.classList.add("editor-item");

        div.innerHTML = `
            <input 
                type="text" 
                class="form__input"
                value="${item}"
                placeholder="Elemento"
                oninput="infoGeneral[${index}] = this.value"
            >
            <button 
                type="button" 
                class="button button--danger"
                onclick="removeInfoGeneral(${index})"
            >
                Eliminar
            </button>
        `;

        container.appendChild(div);
    });
}

function addInfoGeneral() {
    infoGeneral.push("");
    renderInfoGeneral();
}

function removeInfoGeneral(index) {
    infoGeneral.splice(index, 1);
    renderInfoGeneral();
}

/* ============================================================ 
    EDITOR VISUAL: LUGARES DE INTERÉS 
============================================================ */ 
function renderLugares() { 
    const container = document.getElementById("lugaresContainer");
    container.innerHTML = ""; 

    lugaresInteres.forEach((item, index) => { 
        const div = document.createElement("div"); 
        div.classList.add("editor-item"); 

        div.innerHTML = ` 
            <input 
                type="text" 
                class="form__input" 
                value="${item}" 
                placeholder="Lugar de interés" 
                oninput="lugaresInteres[${index}] = this.value" 
            > 
            <button 
                type="button" 
                class="button button--danger" 
                onclick="removeLugar(${index})" 
            > 
                Eliminar 
            </button> `
        ; 

        container.appendChild(div); 
    }); 
} 

function addLugar() { 
    lugaresInteres.push(""); 
    renderLugares(); 
} 

function removeLugar(index) { 
    lugaresInteres.splice(index, 1); 
    renderLugares(); 
}

/* ============================================================
   EDITOR VISUAL: ICONOS INCLUIDOS
============================================================ */
function renderDatosNumericos() { 
    document.getElementById("metrosCuadradosInput").value = metrosCuadrados; 
    document.getElementById("maxHuespedesInput").value = maxHuespedes; 
    document.getElementById("numHabitacionesInput").value = numHabitaciones; 
    document.getElementById("numBanosInput").value = numBanos; 
    document.getElementById("edadBebesGratisInput").value = edadBebesGratis; 
}

function renderIconosActivables() { 
    const mapping = [ 
        { id: "iconoGaraje", var: "iconoGaraje" }, 
        { id: "iconoMascotas", var: "iconoMascotas" }, 
        { id: "iconoChimenea", var: "iconoChimenea" }, 
        { id: "iconoBarbacoa", var: "iconoBarbacoa" }, 
        { id: "iconoJardin", var: "iconoJardin" }, 
        { id: "iconoWifi", var: "iconoWifi" }, 
        { id: "iconoEquipado", var: "iconoEquipado" }, 
        { id: "iconoCalefaccion", var: "iconoCalefaccion" } 
    ]; 

    mapping.forEach(item => { 
        document.getElementById(item.id).checked = (eval(item.var) == 1);
    }); 
}

/* ============================================================
   EDITOR VISUAL: POLÍTICAS DE RESERVA
============================================================ */
function renderPoliticas() {
    const container = document.getElementById("politicasContainer");
    container.innerHTML = "";

    politicasReserva.forEach((item, index) => {
        const div = document.createElement("div");
        div.classList.add("editor-item");

        div.innerHTML = `
            <input 
                type="text" 
                class="form__input"
                value="${item}"
                placeholder="Política de reserva"
                oninput="politicasReserva[${index}] = this.value"
            >
            <button 
                type="button" 
                class="button button--danger"
                onclick="removePolitica(${index})"
            >
                Eliminar
            </button>
        `;

        container.appendChild(div);
    });
}

function addPolitica() {
    politicasReserva.push("");
    renderPoliticas();
}

function removePolitica(index) {
    politicasReserva.splice(index, 1);
    renderPoliticas();
}

/* ============================
   GALERÍA
============================ */
function renderGaleria() {
    const container = document.getElementById("galeriaContainer");
    container.innerHTML = "";

    galeria.forEach((item, index) => {
        const div = document.createElement("div");
        div.classList.add("editor-item");

        div.innerHTML = ` 
            <label>Archivo</label> 
            <input type="text" class="form__input" value="${item.src || ''}" 
                onchange="updateGaleria(${index}, 'src', this.value)"> 
            
            <label>Título</label> 
            <input type="text" class="form__input" value="${item.titulo || ''}" 
                onchange="updateGaleria(${index}, 'titulo', this.value)"> 
            
            <button class="button button--danger" onclick="removeFotoGaleria(${index})"> 
                Eliminar 
            </button> 
            `;

        container.appendChild(div);
    });
}

function addFotoGaleria() {
    galeria.push({ src: "", titulo: "" });
    renderGaleria();
}

function removeFotoGaleria(index) {
    galeria.splice(index, 1);
    renderGaleria();
}

/* ============================================================
   GUARDAR ANTES DE ENVIAR EL FORMULARIO
============================================================ */

document.addEventListener("DOMContentLoaded", () => {
    // Renderizar los bloques
    renderInfoGeneral();
    renderLugares(); 
    renderDatosNumericos(); 
    renderIconosActivables();
    renderPoliticas();
    renderGaleria();

    // Capturar el envío del formulario
    document.querySelector("form").addEventListener("submit", () => {
        // Actualizar los datos 
        // Convertir JS en JSON para la BD
        document.getElementById("informacionGeneralInput").value = JSON.stringify(infoGeneral);
        document.getElementById("lugaresInteresInput").value = JSON.stringify(lugaresInteres);
        document.getElementById("politicasReservaInput").value = JSON.stringify(politicasReserva);
        document.getElementById("galeriaInput").value = JSON.stringify(galeria);
        // Recoger números
        metrosCuadrados = parseInt(document.getElementById("metrosCuadradosInput").value); 
        maxHuespedes = parseInt(document.getElementById("maxHuespedesInput").value); 
        numHabitaciones = parseInt(document.getElementById("numHabitacionesInput").value); 
        numBanos = parseInt(document.getElementById("numBanosInput").value); 
        edadBebesGratis = parseInt(document.getElementById("edadBebesGratisInput").value); 
        // Recoger checkboxes
        iconoGaraje = document.getElementById("iconoGaraje").checked ? 1 : 0; 
        iconoMascotas = document.getElementById("iconoMascotas").checked ? 1 : 0; 
        iconoChimenea = document.getElementById("iconoChimenea").checked ? 1 : 0; 
        iconoBarbacoa = document.getElementById("iconoBarbacoa").checked ? 1 : 0; 
        iconoJardin = document.getElementById("iconoJardin").checked ? 1 : 0; 
        iconoWifi = document.getElementById("iconoWifi").checked ? 1 : 0; 
        iconoEquipado = document.getElementById("iconoEquipado").checked ? 1 : 0; 
        iconoCalefaccion = document.getElementById("iconoCalefaccion").checked ? 1 : 0;
    });
});

