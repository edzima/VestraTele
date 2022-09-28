<template>
    <FullCalendar
        ref="fullCalendar"
        :eventRender="renderItem"
        :eventSources="eventSources"
        v-bind="fullCalendarProps"
        @dateClick="handleDateClick"
        @eventClick="clickEvent"
        @eventDrop="handleChangeDates"
        @eventResize="handleChangeDates"
    />
</template>

<script lang="ts">
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import dayGridPlugin from '@fullcalendar/daygrid';
import listWeekPlugin from '@fullcalendar/list';
import plLang from '@fullcalendar/core/locales/pl';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';

import {DateClickInfo, EventInfo, EventObject, EventSourceObject} from "@/types/FullCalendar";

import 'tippy.js/dist/tippy.css';
import tippy, {Instance, Props as TooltipOptions} from "tippy.js";

const FullCalendar = require('@fullcalendar/vue').default;

const defaultCalendarOptions: any = {
    plugins: [dayGridPlugin, listWeekPlugin, timeGridPlugin, interactionPlugin],
    header: {
        left: 'today prev,next',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,dayGridDay'
    },
    timeZone: 'Europe/Warsaw',
    eventLimit: 5, // for all non-TimeGrid views
    defaultView: 'timeGridWeek',
    locale: plLang,
    droppable: false, // external calendars events
    minTime: '06:00:00',
    maxTime: '23:00:00',
    businessHours: {
        daysOfWeek: [1, 2, 3, 4, 5],

        startTime: '08:00',
        endTime: '16:00',
    },
    showNonCurrentDates: false,
    nowIndicator: true, // red line with current time
    eventTimeFormat: {
        // like '14:30:00'
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    },
    columnHeaderFormat: {
        weekday: 'short',
        month: 'numeric',
        day: 'numeric',
        omitCommas: true
    },
    height: 'auto',
    displayEventTime: false,
    disableResizing: true,
    eventDurationEditable: false, // Disable Resize
};

@Component({
    components: {
        FullCalendar
    }
})
export default class Calendar extends Vue {

    @Prop({type: Function}) eventRender!: Function;
    @Ref() fullCalendar!: any;
    @Prop() private readonly eventSources!: EventSourceObject[];
    @Prop({
        default: true,
        type: Boolean,
    })
    private editable!: boolean; // allow user to edit events
    @Prop({
        default: () => {
        }
    }) private readonly options!: any;


    @Prop({
        default: () => {
            return {
                onShow: (instance: Instance) => {
                    const classList = instance.reference.classList;
                    return !classList.contains('fc-dragging') && classList.contains('fc-allow-mouse-resize') && classList.contains('fc-start');
                },
                delay: [400, 0]
            }


        }
    }) private readonly tooltipOptions!: TooltipOptions;
    private currentShowTippy?: Instance;

    private clickCheckerId: number | undefined = undefined;

    get fullCalendarProps(): any {
        const options = Object.assign(defaultCalendarOptions, this.options);

        return Object.assign(options, {
            editable: this.editable, // FLAG FROM PROPS
        });
    }

    renderItem(info: EventInfo): boolean {
        if (this.eventRender) {
            if (this.eventRender(info)) {
                this.parseTooltip(info);
                return true;
            }
        }
        return false;
    }

    public update(): void {
        this.fullCalendar.getApi().getEventSourceById(1).refetch();
    }

    rerenderEvents(): void {
        this.fullCalendar.getApi().rerenderEvents();
    }

    refeatch(sourceId: number): void {
        this.fullCalendar.getApi().getEventSourceById(sourceId).refetch();
    }

    public updateCalendarEventProp(eventToUpdate: EventObject, propName: string, value: string | number | Function): void {
        eventToUpdate.setProp(propName, value);
    }

    public findCalendarEvent(eventId: number): EventObject {
        return this.fullCalendar.getApi().getEventById(eventId);
    }


    public deleteEventById(eventId: number) {
        const toDel = this.findCalendarEvent(eventId);
        this.deleteEvent(toDel);
    }

    public deleteEvent(eventToDel: EventObject): void {
        eventToDel.remove();
    }


    public createEvent(event: EventObject) {
        this.fullCalendar.getApi().addEvent(event);
    }

    private parseTooltip(info: EventInfo) {
        if (info.event.extendedProps.tooltipContent) {
            const options = Object.assign({content: info.event.extendedProps.tooltipContent}, this.tooltipOptions);
            tippy(info.el, options);
        }
    }

    private handleDateClick(dateClick: DateClickInfo): void {
        if (!this.clickCheckerId) {
            this.clickCheckerId = setTimeout(() => {
                this.removeClickTimeout();
                this.emitExtendedDateClick(dateClick, 'single')
            }, 200);
        } else {
            this.removeClickTimeout();
            this.emitExtendedDateClick(dateClick, 'double')
        }
    }

    private emitExtendedDateClick(dateClick: DateClickInfo, type: 'single' | 'double'): void {
        const dateClickWithDayEvents = this.addDayEventsToEvent(dateClick, dateClick.date);
        this.$emit(type === 'single' ? 'dateClick' : 'dateDoubleClick', dateClickWithDayEvents);
    }

    private removeClickTimeout(): void {
        clearTimeout(this.clickCheckerId);
        this.clickCheckerId = undefined;
    }

    private addDayEventsToEvent(event: any, date: Date): any {
        return {
            ...event,
            dayEvents: this.getDayEvents(date)
        };
    }

    private getDayEvents(date: Date): EventObject[] {
        const fcApi = this.fullCalendar.getApi();
        const activeViewEcents: EventObject[] = fcApi.getEvents();
        const dateString = date.toDateString();
        return activeViewEcents.filter((event: EventObject) => event.start.toDateString() === dateString);
    }

    private handleChangeDates(e: any): void {
        if (!this.editable) return e.revert(); // cancel if no permissions
        if (e.oldEvent) { // cancel if note is dragged to event and vice-versa
            if (e.event.allDay !== e.oldEvent.allDay) return e.revert();
        }
        this.$emit('eventEdit', e);
    }

    private clickEvent(e: EventInfo): void {
        const withEvents = this.addDayEventsToEvent(e, e.event.start)
        this.$emit('eventClick', withEvents);
    }

}
</script>

<style lang='less'>
@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';
@import '~@fullcalendar/timegrid/main.css';


.event-badge {
    position: absolute;
    height: 2rem;
    z-index: 10;
    top: -0.5rem;
    right: -0.5rem;
    border-radius: 1rem;
    box-shadow: 0 0 4px 1px rgba(0, 0, 0, 0.8);
    padding: 0 0.5rem;
    line-height: 2rem;
}


</style>
