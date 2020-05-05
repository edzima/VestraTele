<template>
	<div class="filter-calendar">
		<BootstrapPopup :title="this.notePopupTitle" outerDissmisable ref="notesPopup">
			<CalendarNotes :notes="dayNotes" :onNoteAdd="addNote" :onNoteDelete="deleteNote" :onNoteUpdate="editNoteText" editable/>
		</BootstrapPopup>
		<Filters
				ref="filters"
				:filters="filtersItems"
				@toggleFilter="toggleFilter"
		/>
		<Calendar
				ref="calendar"
				:eventSources="eventSources"
				:eventRender="eventRender"
				:allowUpdate="allowUpdate"
				@dateClick="dateClick"
				@dateDoubleClick="dateDoubleClick"
				@eventEdit="updateDates"
		/>
	</div>
</template>

<script lang="ts">
    import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
    import {DateClickWithDayEvents, EventObject, EventSourceObject, Info} from "@/types/FullCalendar";

    import Calendar from '@/components/Calendar.vue';
    import Filters from '@/components/Filters.vue';
    import {dateToW3C, prettify} from '@/helpers/dateHelper.ts';
    import {telLink} from "@/helpers/HTMLHelper";
    import {Filter, FiltersCollection} from "@/types/Filter";
    import CalendarNotes from "@/components/CalendarNotes.vue";
    import {NoteInterface} from "@/components/Note.vue";
    import BootstrapPopup, {PopupInterface} from "@/components/BootstrapPopup.vue";


    interface MeetEvent extends EventObject {
        statusId: number;
    }

    const defaultFilters: Filter[] = [
        {
            id: 10,
            isActive: true,
            label: 'Umowiony',
            itemOptions: {
                color: 'blue'
            }
        },
        {
            id: 20,
            isActive: true,
            label: 'Podpisana',
            itemOptions: {
                color: 'green'
            }
        },
        {
            id: 40,
            isActive: true,
            label: 'Niepodpisany',
            itemOptions: {
                color: 'purple'
            }
        },
        {
            id: 50,
            isActive: true,
            label: 'Ponowic',
            itemOptions: {
                color: 'red'
            }
        }
    ];


    @Component({
        components: {
            BootstrapPopup,
            CalendarNotes,
            Calendar,
            Filters
        }
    })
    export default class App extends Vue {

        @Prop({
            default: () => defaultFilters
        }) filtersItems!: Filter[];

        @Ref() calendar!: Calendar;
        @Ref() filters!: FiltersCollection;
        @Ref() notesPopup!: PopupInterface;

        private visibleStatusIds: number[] = [];

        private dayNotes: NoteInterface[] = [];
        private notePopupTitle: string = '';
        private notePopupDate!: Date;

        mounted(): void {
            this.visibleStatusIds = this.filters.getActiveFiltersIds();
        }

        get eventSources(): EventSourceObject[] {
            return [
                {
                    id: 0,
                    url: this.URLGetEvents,
                    allDayDefault: false,
                    extraParams: {
                        agentId: this.agentId
                    },
                    success: (data: MeetEvent[]) => {
                        return data.map((event) => {
                            const filter = this.filters.getFilter(event.statusId);
                            if (filter && filter.itemOptions) {
                                event = Object.assign(event, filter.itemOptions);
                            }
                            return event;
                        })
                    }
                }, {
                    id: 1,
                    url: this.URLGetNotes,
                    extraParams: {
                        agentId: this.agentId
                    },
                    allDayDefault: true,
                }
            ];
        }

        //@todo move to filter calendar?
        private toggleFilter(filter: Filter, ids: number[]): void {
            this.visibleStatusIds = ids;
            this.calendar.rerenderEvents();
        }


        eventRender(info: Info): void {
            this.parseVisible(info);
            this.parsePhone(info);
        }

        //@todo move to filter calendar?
        private parseVisible(info: Info): void {
            const status = info.event.extendedProps.statusId;
            if (status) {
                if (this.visibleStatusIds.includes(status)) {
                    info.el.classList.remove('hide');
                } else {
                    info.el.classList.add('hide')
                }
            }
        }

        private parsePhone(info: Info): void {
            const phone = info.event.extendedProps.phone;
            if (phone) {
                const title = info.el.querySelector('.fc-title');
                if (title) {
                    title.innerHTML = telLink(phone, info.event.title).outerHTML;
                }
            }
        }

        private dateClick(dateInfo: DateClickWithDayEvents): void {
            if (!dateInfo.allDay) return; //it's not a note
            if (dateInfo.view.type === 'dayGridMonth') return;
            this.dayNotes = this.getNotesFromDayInfo(dateInfo);
            this.notePopupTitle = 'Notatki ' + prettify(dateInfo.dateStr);
            this.notePopupDate = dateInfo.date;
            this.notesPopup.show();
        }

        private getNotesFromDayInfo(dateInfo: DateClickWithDayEvents): NoteInterface[] {
            return dateInfo.dayEvents
                .filter((event) => event.allDay)
                .map((event: EventObject) => ({
                    content: event.title,
                    id: event.id
                }));
        }

        private dateDoubleClick(dateInfo: DateClickWithDayEvents): void {
            if (dateInfo.allDay) return; //its a note
            this.addEvent(dateInfo.date);
        }

        private async deleteNote(noteToDel: NoteInterface): Promise<boolean> {
            const params: URLSearchParams = new URLSearchParams();
            params.append('id', String(noteToDel.id));
            params.append('agent_id', String(this.agentId));
            const res = await this.axios.post(this.URLDeleteNote, params);
            if (res.status !== 200) return false;
            this.calendar.deleteEventById(noteToDel.id);
            return true;
        }

        private async addNote(newNote: NoteInterface): Promise<number | false> {
            const params: URLSearchParams = new URLSearchParams();
            params.append('news', newNote.content);
            params.append('agent_id', String(this.agentId));
            params.append('date', dateToW3C(this.notePopupDate));

            const res = await this.axios.post(this.URLNewNote, params);
            if (res.status !== 200) return false;
            if (!res.data.id) return false;
            this.calendar.update();
            return res.data.id;
        }

        private addEvent(date: Date): void {
            window.open(`${this.URLAddEvent}?date=${dateToW3C(date)}`);
        }

        private async editNoteText(newNote: NoteInterface): Promise<boolean> {
            const params: URLSearchParams = new URLSearchParams();
            params.append('news', newNote.content);
            params.append('agent_id', String(this.agentId));
            params.append('id', String(newNote.id));

            const res = await this.axios.post(this.URLUpdateNote, params);
            if (res.status !== 200) return false;
            if (res.data.success === false) return false;

            let calendarEvent = this.calendar.findCalendarEvent(newNote.id);
            this.calendar.updateCalendarEventProp(calendarEvent, 'title', newNote.content);
            this.calendar.rerenderEvents();
            return true;
        }


        private async updateDates(e: any): Promise<void> {
            const isNote: boolean = Boolean(e.event.allDay);
            const dateFrom: string = dateToW3C(e.event.start);
            const dateTo: string = dateToW3C(e.event.end);

            const params: URLSearchParams = new URLSearchParams();
            params.append('id', String(e.event.id));
            params.append(isNote ? 'start' : 'date_at', String(dateFrom));
            params.append(isNote ? 'end' : 'date_end_at', String(dateTo));
            const res = await this.axios.post(isNote ? this.URLUpdateNote : this.URLUpdateEvent, params);
            if (res.status !== 200 || !res.data.success) return e.revert();
            if (isNote) {
                //@todo update note sources
            }

        }

        @Prop({
            default: () => true // to change
        })
        private allowUpdate!: boolean; // allow user to edit events

        @Prop({
            required: true
        })
        private agentId!: number;

        @Prop({
            default: () => '/meet-calendar/list'
        })
        private URLGetEvents!: string;


        @Prop({
            default: () => '/meet-calendar/update'
        })
        private URLUpdateEvent!: string;


        @Prop({
            default: () => '/meet/create'
        })
        private URLAddEvent!: string;


        @Prop({
            default: () => '/calendar-note/list'
        })
        private URLGetNotes!: string;


        @Prop({
            default: () => '/calendar-note/add'
        })
        private URLNewNote!: string;


        @Prop({
            default: () => '/calendar-note/update'
        })
        private URLUpdateNote!: string;

        @Prop({
            default: () => '/calendar-note/delete'
        })
        private URLDeleteNote!: string;
    }
</script>
<style lang="less">
	.tel-link {
		display: inline-flex;
		color: white;
		padding-bottom: 10px;
	}
</style>
