<template>
    <div class="filter-calendar">
        <BootstrapPopup :title="this.notePopupTitle" outerDissmisable ref="notesPopup" v-if="notesEnabled">
            <CalendarNotes
                :URLDelete="URLDeleteNote"
                :notes="dayNotes"
                :onNoteAdd="addNote"
                :onNoteUpdate="editNoteText"
                editable
                @deleteNote="deleteNote"
            />
        </BootstrapPopup>

        <FilterCalendar
            :filterGroups="filterGroups"
            :notesEnabled="notesEnabled"
            :eventSources="eventSources"
            :editable="allowUpdate"
            :calendarOptions="calendarOptions"
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

import FilterManager from '@/components/FilterManager.vue';
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
        BootstrapPopup,
        CalendarNotes,
        FilterManager,
        FilterCalendar
    }
})
export default class App extends Vue {

    @Prop({
        default: () => []
    }) filterGroups!: FilterGroup[];

    @Prop({
        default: () => {
        }
    }) private calendarOptions!: any;

    @Prop({
        default: () => true // to change
    })
    private allowUpdate!: boolean; // allow user to edit events

    @Prop({
        required: false,
        default: () => []
    })
    private extraHTTPParams!: ExtraParam[];

    @Prop()
    private URLAddEvent!: string;

    @Prop({
        default: () => true
    })
    private notesEnabled!: boolean;

    @Prop()
    private URLGetNotes!: string;

    @Prop()
    private URLCreateNote!: string;

    @Prop()
    private URLUpdateNote!: string;

    @Prop()
    private URLDeleteNote!: string;

    @Prop({
        default: () => 100
    })
    private NoteResourceId!: number;

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
            .filter((event: EventObject) => {
                return event.allDay && this.eventIsNote(event)
            })
            .map((event: EventObject) => ({
                content: event.title,
                id: event.id,
                update: event.extendedProps.update,
                delete: event.extendedProps.delete
            }));
    }

    private eventClick(event: EventInfo) {
        if (this.notesEnabled && this.eventIsNote(event.event)) {
            this.dateClick(event)
        }
    }

    private eventIsNote(event: EventObject): boolean {
        return event?.source?.id == this.NoteResourceId;
    }

    private dateClick(dateInfo: any): void {
        if (!this.notesEnabled) return;
        const date = dateInfo.date ? dateInfo.date : dateInfo.event.start;
        this.dayNotes = this.getNotesFromDayInfo(dateInfo);
        this.notePopupTitle = 'Notatki ' + date.toLocaleDateString();
        this.notePopupDate = date;
        this.notesPopup.show();
    }

    private deleteNote(noteToDel: NoteInterface): void {
        this.calendarFilter.deleteEventById(noteToDel.id!);
    }

    private dateDoubleClick(dateInfo: DateClickWithDayEvents): void {
        if (dateInfo.allDay) return; //it's a note
        this.addEvent(dateInfo.date);
    }

    private addEvent(date: Date): void {
        const dateString = date.toJSON();
        window.open(`${this.URLAddEvent}?date=${dateString}`);
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
        params.append('date', this.notePopupDate.toJSON());
        this.extraHTTPParams.forEach((param: ExtraParam) => {
            params.append(param.name, String(param.value));
        })

        const res = await this.axios.post(this.URLCreateNote, params);
        if (res.status !== 200) return false;
        if (!res.data.id) return false;
        this.resourceNotes();
        return res.data.id;
    }


    private resourceNotes(): void {
        if (this.notesEnabled) {
            this.calendarFilter.calendar.refeatch(this.NoteResourceId);
        }
    }

    private async updateDates(e: any): Promise<void> {
        ignoreAllSelections();
        hideAllTippy();
        const event: EventObject = e.event;
        const isNote = this.eventIsNote(event);
        if (isNote && !event.extendedProps.update) {
            e.revert();
            return;
        }

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
