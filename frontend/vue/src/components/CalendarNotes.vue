<template>
	<div class="calendar-notes">
		<template v-if="isOperation('view')">
			<NotesList :notes="localNotes" @deleteClick="deleteNote" @editClick="editNote" confirmDelete editable v-if="operation==='view'"/>
			<Button @click="addNote">Dodaj Notatkę</Button>
		</template>
		<template v-if="isOperation('edit')">
			<NoteEditor :confirmEdit="noteToEdit.id" :note="noteToEdit" @discardChanges="discardEditChanges" @saveEditedNote="noteChangesHandler" auto-focus/>
		</template>
	</div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import BootstrapPopup from "@/components/BootstrapPopup.vue";
    import {NoteInterface} from "@/components/Note.vue";
    import NoteEditor from "@/components/NoteEditor.vue";
    import NotesList from "@/components/NotesList.vue";

    @Component({
        components: {NoteEditor, NotesList, BootstrapPopup}
    })
    export default class CalendarNotes extends Vue {

        @Prop() onNoteUpdate: Function | undefined;
        @Prop() onNoteDelete: Function | undefined;
        @Prop() onNoteAdd: Function | undefined;
        @Prop({default: () => []}) notes!: NoteInterface[];
        @Prop({default: () => false}) editable!: boolean;

        private noteToEdit: NoteInterface | null = null;
        private operation: 'view' | 'edit' = 'view';
        private localNotes: NoteInterface[] = [];

        mounted(): void {
            this.localNotes = [...this.notes];
        }

        private isOperation(name: string): boolean {
            return name === this.operation;
        }

        private editNote(noteToEdit: NoteInterface): void {
            this.noteToEdit = noteToEdit;
            this.operation = "edit";
        }

        private addNote() {
            this.operation = "edit";
            this.noteToEdit = {id: null, content: ''};
        }

        private async deleteNote(noteToDel: NoteInterface): Promise<void> {
            const success = typeof this.onNoteUpdate === "function" ? await this.onNoteDelete!(noteToDel) : true;
            if (!success) return;
            this.removeNoteFromList(noteToDel);
        }

        private noteChangesHandler(note: NoteInterface): void {
            if (note.id) {
                this.saveEditedNote(note);
            } else {
                this.addAndSaveNote(note);
            }
            this.noteToEdit = {id: null, content: ''};
            this.operation = "view";
        }

        private async addAndSaveNote(note: NoteInterface): Promise<void> {
            const noteId = typeof this.onNoteAdd === "function" ? await this.onNoteAdd!(note) : true;
            if (!noteId) return;
            this.addNoteToList({id: noteId, content: note.content});
        }

        private addNoteToList(newNote: NoteInterface) {
            this.localNotes.push(newNote);
        }

        private async saveEditedNote(newNote: NoteInterface): Promise<void> {
            const success = typeof this.onNoteUpdate === "function" ? await this.onNoteUpdate!(newNote) : true;
            if (!success) return;
            this.updateNoteInList(newNote);
        }

        private updateNoteInList(newNote: NoteInterface): void {
            this.localNotes = this.localNotes.map((note: NoteInterface) => {
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
            this.localNotes = this.localNotes.filter((note: NoteInterface) => note.id !== noteToDel.id);
        }

        private discardEditChanges(): void {
            this.noteToEdit = {id: null, content: ''};
            this.operation = "view";
        }
    }
</script>

<style lang="less">
	.title-date {
		font-weight: bold;
	}
</style>
