document.addEventListener("DOMContentLoaded", () => {
  // Obtener el contenedor del calendario
  const calendarEl = document.getElementById("calendario");
  if (!calendarEl) return;

  // Inicializar FullCalendar
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth", // Vista inicial: mes
    locale: "es", // Calendario en español
    aspectRatio: 4.0, // Proporción ancho/alto
    contentHeight: "auto", // Ajuste automático de altura
    expandRows: true, // Evita que se compriman las filas
    
    // Barra superior del calendario
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay"
    },

    // Bloquear navegacion fechas anteriores
    validRange: {
      start: new Date() 
    },

    // Cargar eventos desde el servidor 
    events: "./get-reservas.php",

    // Acción al hacer clic en una reserva
    eventClick: function(info) {
      alert(`Reserva: ${info.event.title}\nEntrada: ${info.event.start.toLocaleDateString()}`);
    }
  });

  // Renderizar el calendario en pantalla
  calendar.render();

  // Guardar referencia global
  window.calendar = calendar; 
});

