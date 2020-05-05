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

    import {DateClickInfo, DateClickWithDayEvents, EventObject, EventSourceObject, Info} from "@/types/FullCalendar";

    import 'tippy.js/dist/tippy.css';
    import tippy, {Props as TooltipOptions} from "tippy.js";
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
            }
        }) private readonly tooltipOptions!: TooltipOptions;

        @Ref() fullCalendar!: any;

        renderItem(info: Info): void {
            if (this.eventRender) {
                this.eventRender(info);
            }
            this.parseTooltip(info);
        }

        public update(): void {
            console.log(this.fullCalendar.getApi().getEventSourceById(1).refetch())
        }

        private parseTooltip(info: Info) {
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

        public preventDuplicateEvents(events: EventObject[]): boolean {
            return events.filter((event: EventObject) => !this.findCalendarEvent(event.id));
        }

        private emitExtendedDateClick(dateClick: DateClickInfo, type: 'single' | 'double'): void {
            const dateClickWithDayEvents = this.AddEventsToDateClick(dateClick);
            this.$emit(type === 'single' ? 'dateClick' : 'dateDoubleClick', dateClickWithDayEvents);
        }

        private removeClickTimeout(): void {
            clearTimeout(this.clickCheckerId);
            this.clickCheckerId = undefined;
        }

        private AddEventsToDateClick(dateClick: DateClickInfo): DateClickWithDayEvents {
            return {
                ...dateClick,
                dayEvents: this.getDayEvents(dateClick.date)
            };
        }

        private getDayEvents(date: Date): EventObject[] {
            const fcApi = this.fullCalendar.getApi();
            const activeViewEcents: EventObject[] = fcApi.getEvents();
            return activeViewEcents.filter((event: EventObject) => isSameDate(event.start, date));
        }

        private handleChangeDates(e: any): void {
            // if (!this.editable) return e.revert(); // cancel if no permissions
            if (e.oldEvent) { // cancel if note is dragged to event and vice-versa
                if (e.event.allDay !== e.oldEvent.allDay) return e.revert();
            }
            this.$emit('eventEdit', e);
        }

        //@todo inspect from event.url
        private inspectEvent(event: any): void {
            return;
            /*
            if (!this.eventClick.timeoutId) {
                this.eventClick.timeoutId = setTimeout(() => {
                    // simple click
                    clearTimeout(this.eventClick.timeoutId);
                    this.eventClick.timeoutId = 0;
                    this.eventClick.eventClicked = event.el;
                }, 200); // tolerance in ms
            } else {
                // double click

                clearTimeout(this.eventClick.timeoutId);
                this.eventClick.timeoutId = 0;
                this.eventClick.eventClicked = event.el;
                this.$emit('eventDoubleClick', event.event.id);
            }

             */
        }
    }
</script>

<style lang='less'>
	@import "~@fullcalendar/core/main.css";
	@import "~@fullcalendar/daygrid/main.css";
	@import "~@fullcalendar/timegrid/main.css";

	.fc-event {
		&a {
			&:hover {
				opacity: 0.5;
			}
		}
	}

</style>
