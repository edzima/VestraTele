<template>
  <div class="calendar">
    <FullCalendar
        ref="fullCalendar"
        v-bind="fullCalendarProps"
        :eventSources="eventSources"
        :eventRender="renderItem"
        @eventDrop="handleChangeDates"
        @eventResize="handleChangeDates"
        @dateClick="handleDateClick"
        @eventClick="clickEvent"
    />
  </div>
</template>

<script lang="ts">
    import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import listWeekPlugin from '@fullcalendar/list';
    import plLang from '@fullcalendar/core/locales/pl';
    import interactionPlugin from '@fullcalendar/interaction';
    import timeGridPlugin from '@fullcalendar/timegrid';

    import {
        DateClickInfo,
        DateClickWithDayEvents,
        EventInfo,
        EventObject,
        EventSourceObject
    } from "@/types/FullCalendar";

    import 'tippy.js/dist/tippy.css';
    import tippy, {Props as TooltipOptions, Instance} from "tippy.js";

    import {isSameDate} from "@/helpers/dateHelper";

    const FullCalendar = require('@fullcalendar/vue').default;

    const defaultCalendarOptions: any = {
        plugins: [dayGridPlugin, listWeekPlugin, timeGridPlugin, interactionPlugin],
        header: {
            left: 'today prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,dayGridDay'
        },
        eventLimit: true, // for all non-TimeGrid views
        defaultView: 'timeGridWeek',
        locale: plLang,
        droppable: false, // external calendars events
        minTime: '06:00:00',
        maxTime: '23:00:00',
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

    };

    @Component({
        components: {
            FullCalendar
        }
    })
    export default class Calendar extends Vue {

        @Prop({type: Function}) eventRender!: Function;

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

  @Ref() fullCalendar!: any;

  private currentShowTippy?: Instance;

  renderItem(info: EventInfo): void {
    if (this.eventRender) {
      this.eventRender(info);
    }
    this.parseTooltip(info);
  }

  public update(): void {
    this.fullCalendar.getApi().getEventSourceById(1).refetch();
  }

  private parseTooltip(info: EventInfo) {
    if (info.event.extendedProps.tooltipContent) {
      const options = Object.assign({content: info.event.extendedProps.tooltipContent}, this.tooltipOptions);
      tippy(info.el, options);
    }
  }

  get fullCalendarProps(): any {
    const options = Object.assign(defaultCalendarOptions, this.options);

    return Object.assign(options, {
      eventDurationEditable: this.editable, // allow to extend time
      editable: this.editable, // FLAG FROM PROPS
    });
  }

  rerenderEvents(): void {
    this.fullCalendar.getApi().rerenderEvents();
  }

  public updateCalendarEventProp(eventToUpdate: EventObject, propName: string, value: string | number | Function): void {
    eventToUpdate.setProp(propName, value);
  }

  public findCalendarEvent(eventId: number): EventObject {
    // const activeViewEcents: EventObject[] = fcApi.getEvents();
    // return activeViewEcents.find((event: EventObject) => event.id === eventId)!;
    return this.fullCalendar.getApi().getEventById(eventId);
  }

  public deleteEvent(eventToDel: EventObject): void {
    eventToDel.remove();
  }

  public deleteEventById(eventId: number) {
    const toDel = this.findCalendarEvent(eventId);
    this.deleteEvent(toDel);
  }

  public createEvent(event: EventObject) {
    this.fullCalendar.getApi().addEvent(event);
  }

  private clickCheckerId: number | undefined = undefined;

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
    const dateClickWithDayEvents = this.addDayEventsToEvent(dateClick,dateClick.date);
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
    return activeViewEcents.filter((event: EventObject) => isSameDate(event.start, date));
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
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";

.tel-link {
  display: inline-flex;
  color: white;
  padding-bottom: 10px;
}
.event-badge {
  position: absolute;
  height: 20px;
  width: 20px;
  z-index: 10;
  top: -5px;
  right: -5px;
  border-radius: 50%;
  box-shadow: 0 0 4px 1px rgba(0, 0, 0, 0.8);
}

.fc-event {
  overflow: visible;
  border: none;
  padding: 2px;

  .fc-content {
    height: 100%;
    overflow: visible;

    &a {
      &:hover {
        opacity: 0.5;
      }
    }
  }
}

</style>
