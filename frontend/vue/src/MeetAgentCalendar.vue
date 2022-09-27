<template>
    <div class="filter-calendar">
        <BootstrapPopup :title="this.notePopupTitle" outerDissmisable ref="notesPopup" v-if="notesEnabled">
            <CalendarNotes :notes="dayNotes" :onNoteAdd="addNote" :onNoteDelete="deleteNote" :onNoteUpdate="editNoteText" editable/>
        </BootstrapPopup>

        <FilterCalendar
            :filterGroups="filterGroups"
            :notesEnabled="notesEnabled"
            :eventSources="eventSources"
            :editable="allowUpdate"
            @dateClick="dateClick"
            @dateDoubleClick="dateDoubleClick"
            @eventEdit="updateDates"
            @eventClick="eventClick"
            ref="calendarFilter"
        />
    </div>
</template>

<script lang="ts">
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import {
    DateClickWithDayEvents,
    EventInfo,
    EventObject,
    EventSourceConfig,
    EventSourceObject
} from "@/types/FullCalendar";

import Calendar from '@/components/Calendar.vue';
import FilterManager from '@/components/FilterManager.vue';
import {dateToW3C, prettify} from '@/helpers/dateHelper.ts';
import {FilterGroup} from "@/types/Filter";
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
    }) filterGroups!: FilterGroup[];

    @Ref() calendar!: Calendar;
    @Ref() calendarFilter!: FilterCalendar;
    @Ref() notesPopup!: PopupInterface;

    private dayNotes: NoteInterface[] = [];
    private notePopupTitle: string = '';
    private notePopupDate!: Date;

    get eventSources(): EventSourceObject[] {
        return [...this.mapEventSources(this.eventSourcesConfig), this.getNotesSettings()]
    }

    private mapEventSources(events: EventSourceConfig[]): EventSourceObject[] {
        return events.map((eventSource: EventSourceConfig) => {
            eventSource.extraParams = mapExtraParamsToObj(this.extraHTTPParams)
            eventSource.success = this.setUpdateUrlMapper(eventSource.urlUpdate)
            return eventSource
        })
    }

    private setUpdateUrlMapper(url: string): Function {
        return (events: any[]) => events.map((event: any) => {
            event.urlUpdate = url
            return event
        })
    }

    @Prop({
        default: () => 100
    })
    private NoteResourceId!: number;

    private getNotesSettings(): EventSourceObject {
        if (!this.notesEnabled) return {};
        return {
            id: this.NoteResourceId,
            url: this.URLGetNotes,
            extraParams: mapExtraParamsToObj(this.extraHTTPParams),
            allDayDefault: true,
        }
    }

    private getNotesFromDayInfo(event: any): NoteInterface[] {
        return event.dayEvents
            .filter((event) => event.allDay)
            .map((event: EventObject) => ({
                content: event.title,
                id: event.id
            }));
    }

    private eventClick(event: EventInfo) {
        if (this.notesEnabled && event.event.extendedProps.isNote) {
            this.dateClick(event)
        }

    }

    private dateClick(dateInfo: any): void {
        // if (dateInfo.view.type === 'dayGridMonth') return;
        if (!this.notesEnabled) return;
        this.dayNotes = this.getNotesFromDayInfo(dateInfo);
        this.notePopupTitle = 'Notatki ' + prettify(dateInfo.dateStr);
        this.notePopupDate = dateInfo.date;
        this.notesPopup.show();
    }

    private async deleteNote(noteToDel: NoteInterface): Promise<boolean> {
        const params: URLSearchParams = new URLSearchParams();
        params.append('id', String(noteToDel.id));
        this.extraHTTPParams.forEach((param: ExtraParam) => {
            params.append(param.name, String(param.value));
        })
        const res = await this.axios.post(this.URLDeleteNote, params);
        if (res.status !== 200) return false;
        this.calendarFilter.deleteEventById(noteToDel.id!);
        return true;
    }

    private dateDoubleClick(dateInfo: DateClickWithDayEvents): void {
        if (dateInfo.allDay) return; //it's a note
        this.addEvent(dateInfo.date);
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

        let calendarEvent = this.calendarFilter.findCalendarEvent(newNote.id!);
        this.calendarFilter.updateCalendarEventProp(calendarEvent, 'title', newNote.content);
        this.calendarFilter.rerenderCalendar();
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
        this.calendarFilter.calendar.refeatch(this.NoteResourceId);
        return res.data.id;
    }

    @Prop({
        default: () => true // to change
    })
    private allowUpdate!: boolean; // allow user to edit events

    @Prop({
        required: false,
        default: () => []
    })
    private extraHTTPParams!: ExtraParam[];

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

    private async updateDates(e: any): Promise<void> {
        ignoreAllSelections();
        hideAllTippy();
        const event: EventObject = e.event;
        const isNote: boolean = event.extendedProps.isNote;

        const url: string = isNote ? this.URLUpdateNote : event.extendedProps.urlUpdate;

        const params: URLSearchParams = new URLSearchParams();
        params.append('id', String(event.id))
        if (event.start) {
            params.append('start_at', event.start.toJSON());
        }
        if (event.end) {
            params.append('end_at', event.end.toJSON());
        }

        try {
            const res = await this.axios.post(url, params);
            if (res.status !== 200) {
                e.revert();
            }
        } catch {
            e.revert();
        }

    }

    @Prop({
        required: true
    })
    private eventSourcesConfig!: EventSourceConfig[]
}
</script>
