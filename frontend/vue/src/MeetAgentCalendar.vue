<template>
	<div class="filter-calendar">
		<BootstrapPopup :title="this.notePopupTitle" outerDissmisable ref="notesPopup" v-if="notesEnabled">
			<CalendarNotes :notes="dayNotes" :onNoteAdd="addNote" :onNoteDelete="deleteNote" :onNoteUpdate="editNoteText" editable/>
		</BootstrapPopup>

        <FilterCalendar
            :filters="filters"
            :eventSources="eventSources"
            :editable="allowUpdate"
            @dateClick="dateClick"
            @dateDoubleClick="dateDoubleClick"
            @eventEdit="updateDates"
            ref="calendarFilter"
        />
	</div>
</template>

<script lang="ts">
    import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
    import {DateClickWithDayEvents, EventObject, EventSourceObject, EventInfo} from "@/types/FullCalendar";

    import Calendar from '@/components/Calendar.vue';
    import FilterManager from '@/components/Filters.vue';
    import {dateToW3C, prettify} from '@/helpers/dateHelper.ts';
    import {telLink} from "@/helpers/HTMLHelper";
    import {Filter} from "@/types/Filter";
    import CalendarNotes from "@/components/CalendarNotes.vue";
    import {NoteInterface} from "@/components/Note.vue";
    import BootstrapPopup, {PopupInterface} from "@/components/BootstrapPopup.vue";
    import {ExtraParam} from "@/types/ExtraParam";
    import {mapExtraParamsToObj} from "@/helpers/extraParamsHelper";
    import {hideAllTippy, ignoreAllSelections} from "@/helpers/domHelper";
    import FilterCalendar from "@/components/FilterCalendar.vue";


    @Component({
        components: {
            FilterCalendar,
            BootstrapPopup,
            CalendarNotes,
            Calendar,
            FilterManager
        }
    })
    export default class App extends Vue {

        @Prop({
            default: () => []
        }) filters!: Filter[];

        @Ref() calendar!: Calendar;
        @Ref() calendarFilter!: FilterCalendar;
        @Ref() notesPopup!: PopupInterface;

        private dayNotes: NoteInterface[] = [];
        private notePopupTitle: string = '';
        private notePopupDate!: Date;

        get eventSources(): EventSourceObject[] {
            return [
                {
                    id: 0,
                    url: this.URLGetEvents,
                    allDayDefault: false,
                    extraParams: mapExtraParamsToObj(this.extraHTTPParams),
                    success: (data) => this.calendarFilter.filterEvents(data)
                }, this.getNotesSettings()
            ];
        }

        private getNotesSettings(): EventSourceObject{
            if(!this.notesEnabled) return {};
            return {
                id: 1,
                url: this.URLGetNotes,
                extraParams: mapExtraParamsToObj(this.extraHTTPParams),
                allDayDefault: true,
            }
        }

        private dateClick(dateInfo: DateClickWithDayEvents): void {
            if (!this.notesEnabled) return;
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
            this.extraHTTPParams.forEach((param: ExtraParam) => {
                params.append(param.name, String(param.value));
            })
            const res = await this.axios.post(this.URLDeleteNote, params);
            if (res.status !== 200) return false;
            this.calendar.deleteEventById(noteToDel.id!);
            return true;
        }

        private async addNote(newNote: NoteInterface): Promise<number | false> {
            const params: URLSearchParams = new URLSearchParams();
            params.append('news', newNote.content);
            params.append('date', dateToW3C(this.notePopupDate));
            this.extraHTTPParams.forEach((param: ExtraParam) => {
                params.append(param.name, String(param.value));
            })

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
            this.extraHTTPParams.forEach((param: ExtraParam) => {
                params.append(param.name, String(param.value));
            })
            params.append('id', String(newNote.id));

            const res = await this.axios.post(this.URLUpdateNote, params);
            if (res.status !== 200) return false;
            if (res.data.success === false) return false;

            let calendarEvent = this.calendar.findCalendarEvent(newNote.id!);
            this.calendar.updateCalendarEventProp(calendarEvent, 'title', newNote.content);
            this.calendar.rerenderEvents();
            return true;
        }


        private async updateDates(e: any): Promise<void> {
            ignoreAllSelections();
            hideAllTippy();
            const isNote: boolean = Boolean(e.event.allDay);
            const dateFrom: string = dateToW3C(e.event.start);
            const dateTo: string = dateToW3C(e.event.end);

            const params: URLSearchParams = new URLSearchParams();
            params.append('id', String(e.event.id));
            params.append(isNote ? 'start' : 'date_at', String(dateFrom));
            params.append(isNote ? 'end' : 'date_end_at', String(dateTo));
            this.extraHTTPParams.forEach((param: ExtraParam) => {
                params.append(param.name, String(param.value));
            })
            let res;
            try{
                res = await this.axios.post(isNote ? this.URLUpdateNote : this.URLUpdateEvent, params);
            }catch{
                e.revert();
            }
            if (res.status !== 200 || !res.data.success){
                e.revert();
            }
            if (isNote) {
                //@todo update note sources
            }

        }

        @Prop({
            default: () => true // to change
        })
        private allowUpdate!: boolean; // allow user to edit events

        @Prop({
            required: false,
            default: ()=> []
        })
        private extraHTTPParams!: ExtraParam[];

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
          default: () => true
        })
        private notesEnabled!: boolean;

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
