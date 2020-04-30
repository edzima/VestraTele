<template>
	<div class="calendar">
		<NotesPopup
				:date="noteOpenedDate"
				@close="closePopup"
				@deleteNote="deleteNote"
				@addNote="addNote"
				@editNoteText="editNoteText"
		/>
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
    import NotesPopup from './NotesPopup.vue';

    import {DateClickInfo, EventSourceObject, Info} from "@/types/FullCalendar";

    import 'tippy.js/dist/tippy.css';
    import tippy, {Props as TooltipOptions} from "tippy.js";

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
            FullCalendar,
            NotesPopup,
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


        private deleteNote(noteID: number): void {
            // handle delete
            this.$emit('deleteNote', noteID);
        }

        private noteOpenedDate: any = null;

        private editNoteText(noteID: number, text: string): void {
            this.$emit('editNoteText', noteID, text);
        }

        private closePopup(): void {
            this.noteOpenedDate = null;
        }

        private openNotes(e: any): void {
            this.noteOpenedDate = e.date ? e.date : e.start;
        }

        private clickCheckerId: number | undefined = undefined;

        private handleDateClick(dateClick: DateClickInfo): void {
            // if its a month view allow only to add events, not notes
            // console.log('dateClick');
            // if (e.view && e.view.type !== 'dayGridMonth') {
            //     if (e.allDay) {
            //         return this.openNotes(e);
            //     }
            // }
            if (!this.clickCheckerId) {
                this.clickCheckerId = setTimeout(() => {
                    // single click
                    clearTimeout(this.clickCheckerId);
                    this.clickCheckerId = undefined;
                    this.$emit('dateClick', dateClick);
                }, 200);
            } else {
                // double click
                clearTimeout(this.clickCheckerId);
                this.clickCheckerId = undefined;
                this.$emit('dateDoubleClick', dateClick);
            }
        }

        private addNote(noteText: string, day: Date): void {
            this.$emit('addNote', noteText, day);
        }


        private handleChangeDates(e: any): void {
            if (!this.editable) return e.revert(); // cancel if no permissions
            if (e.oldEvent) { // cancel if note is dragged to event and vice-versa
                if (e.event.allDay !== e.oldEvent.allDay) return e.revert();
            }
            this.$emit('eventEdit', e);
        }

        //@todo inspect from event.url
        private inspectEvent(event: any): void {
            if (event.event.allDay) {
                this.openNotes(event.event);
            }
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
