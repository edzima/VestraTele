<template>
	<BootstrapPopup ref="popup">
		<template v-slot:header>
			<button @click.prevent="closeCalendarNotes" aria-label="Close" class="close" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title">Notatki z dnia <span class="text-primary title-date">{{pettyDayDate}}</span></h4>
		</template>
		<template v-slot:body>
			<EditableNotesList :notes="notes" @deleteNote="deleteNote" @editNote="editNote" confirmDelete v-if="operation==='view'"/>
			<NoteEditor :note="noteToEdit" @discardChanges="discardEditChanges" @saveEditedNote="saveEditedNote" auto-focus confirmEditSave v-if="operation==='edit'"/>
		</template>
		<template v-slot:footer>
		</template>
	</BootstrapPopup>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import BootstrapPopup, {PopupInterface} from "@/components/BootstrapPopup.vue";
    import {DateClickWithDayEvents, EventObject} from "@/types/FullCalendar";
    import EditableNotesList from "@/components/EditActionsNotesList.vue";
    import {NoteInterface} from "@/components/Note.vue";
    import {prettify} from "@/helpers/dateHelper";
    import NoteEditor from "@/components/NoteEditor.vue";

    export interface NotesPopupInterface extends Element {
        readonly openCalendarDayNotes: Function;
        readonly closeCalendarNotes: Function;
    }

    @Component({
        components: {NoteEditor, EditableNotesList, BootstrapPopup}
    })
    export default class CalendarNotesPopup extends Vue {

        @Prop() onNoteUpdate: Function | undefined;
        @Prop() onNoteDelete: Function | undefined;
        private noteToEdit: NoteInterface | null = null;

        private operation: 'view' | 'edit' = 'view';
        private notes: NoteInterface[] = [];
        private pettyDayDate: string = '';

        get popup(): PopupInterface {
            return (this.$refs.popup as PopupInterface);
        }

        public openCalendarDayNotes(dayInfo: DateClickWithDayEvents): void {
            this.operation = "view";
            this.notes = this.getNotesFromDayInfo(dayInfo);
            this.pettyDayDate = prettify(dayInfo.dateStr);
            this.popup.show();
        }

        public closeCalendarNotes(): void {
            this.popup.hide();
        }

        private getNotesFromDayInfo(dayInfo: DateClickWithDayEvents): NoteInterface[] {
            return dayInfo.dayEvents.map((event: EventObject) => ({
                content: event.title,
                id: event.id
            }))
        }

        private editNote(noteToEdit: NoteInterface): void {
            this.noteToEdit = noteToEdit;
            this.operation = "edit";
        }

        private async deleteNote(noteToDel: NoteInterface): Promise<void> {
            const success = this.hasProp('onNoteDelete') ? await this.onNoteDelete!(noteToDel) : true;
            if (!success) return;
            this.removeNoteFromList(noteToDel);
        }

        private async saveEditedNote(newNote: NoteInterface): Promise<void> {
            this.noteToEdit = {};
            this.operation = "view";
            const success = this.hasProp('onNoteUpdate') ? await this.onNoteUpdate!(newNote) : true;
            if (!success) return;
            this.updateNoteInList(newNote);
        }

        private hasProp(propName: any): boolean {
            return !!this.$props[propName];
        }

        private updateNoteInList(newNote: NoteInterface): void {
            this.notes = this.notes.map((note: NoteInterface) => {
                if (note.id === newNote.id) {
                    return {
                        ...note,
                        content: newNote.content
                    }
                } else {
                    return note;
                }
            })
        }

        private removeNoteFromList(noteToDel: NoteInterface): void {
            this.notes = this.notes.filter((note: NoteInterface) => note.id !== noteToDel.id);
        }

        private discardEditChanges(): void {
            this.noteToEdit = {};
            this.operation = "view";
        }
    }
</script>

<style lang="less">
	.title-date {
		font-weight: bold;
	}
</style>
