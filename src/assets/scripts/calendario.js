document.addEventListener("DOMContentLoaded", () => {
  const calendarEl = document.getElementById("calendario");
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    locale: "es",
    aspectRatio: 4.0,
    contentHeight: "auto",   
    expandRows: true,    
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay"
    },
    validRange: {
      start: new Date() // Bloquear navegacion fechas anteriores
    },
    events: "./get-reservas.php",
    eventClick: function(info) {
      alert(`Reserva: ${info.event.title}\nEntrada: ${info.event.start.toLocaleDateString()}`);
    }
  });

  calendar.render();
  window.calendar = calendar; // Disponible globalmente
});

