import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";

/**
 * Exposed to window so the Blade inline script can call it after
 * Vite's app.js has finished loading.
 */
window.ReunionesCalendar = {
    instance: null,

    /**
     * @param {Array} events  Pre-built events array
     * @param {Object} callbacks { onEventClick, onDateClick, onEventDrop }
     */
    init(events, callbacks) {
        const el = document.getElementById("calendar");
        if (!el) return;

        this.instance = new Calendar(el, {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
            locale: esLocale,
            initialView: "dayGridMonth",
            height: "auto",
            dayMaxEvents: 3,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,listWeek",
            },
            buttonText: { today: "Hoy", month: "Mes", week: "Semana", list: "Lista" },
            events: events,
            editable: true,
            droppable: true,
            selectable: true,
            eventClick: (info) => callbacks.onEventClick && callbacks.onEventClick(info),
            dateClick:  (info) => callbacks.onDateClick  && callbacks.onDateClick(info),
            eventDrop:  (info) => callbacks.onEventDrop  && callbacks.onEventDrop(info),
        });

        this.instance.render();
    },

    setEvents(events) {
        if (!this.instance) return;
        this.instance.removeAllEvents();
        this.instance.addEventSource(events);
    },

    updateSize() {
        if (this.instance) this.instance.updateSize();
    }
};
