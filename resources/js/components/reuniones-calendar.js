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
     * @param {Array}  events     Pre-built events array
     * @param {Object} callbacks  { eventContent, onEventClick, onDateClick, onEventDrop }
     */
    init(events, callbacks) {
        const el = document.getElementById("calendar");
        if (!el) return;

        const config = {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
            locale: esLocale,
            initialView: "dayGridMonth",
            height: "auto",
            dayMaxEvents: 4,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,listWeek",
            },
            buttonText: { today: "Hoy", month: "Mes", week: "Semana", list: "Lista" },
            events: events,
            editable: true,
            droppable: true,
            eventClick: (info) => callbacks.onEventClick && callbacks.onEventClick(info),
            dateClick:  (info) => callbacks.onDateClick  && callbacks.onDateClick(info),
            eventDrop:  (info) => callbacks.onEventDrop  && callbacks.onEventDrop(info),
            eventMouseEnter: (info) => callbacks.onEventMouseEnter && callbacks.onEventMouseEnter(info),
            eventMouseLeave: (info) => callbacks.onEventMouseLeave && callbacks.onEventMouseLeave(info),
        };

        // Support custom event content renderer (ClickUp-style cards)
        if (callbacks.eventContent) {
            config.eventContent = (info) => callbacks.eventContent(info);
        }

        this.instance = new Calendar(el, config);
        this.instance.render();
    },

    setEvents(events) {
        if (!this.instance) return;
        this.instance.removeAllEvents();
        this.instance.addEventSource(events);
    },

    /** Re-apply events AND update the eventContent renderer at the same time */
    setEventsWithContent(events, contentFn) {
        if (!this.instance) return;
        if (contentFn) {
            this.instance.setOption("eventContent", (info) => contentFn(info));
        }
        this.instance.removeAllEvents();
        this.instance.addEventSource(events);
    },

    updateSize() {
        if (this.instance) this.instance.updateSize();
    }
};
