<template>
	<div class="calendar-notes">
		<NotesList :notes="notes" @deleteClick="deleteNote" @editClick="editNote" confirmDelete editable v-if="operation==='view'"/>
		<NoteEditor :note="noteToEdit" @discardChanges="discardEditChanges" @saveEditedNote="saveEditedNote" auto-focus confirmEdit v-else-if="operation==='edit'"/>
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
        @Prop({default: () => []}) notes!: NoteInterface[];
        @Prop({default: () => false}) editable!: boolean;

        private noteToEdit: NoteInterface | null = null;
        private operation: 'view' | 'edit' = 'view';

        private editNote(noteToEdit: NoteInterface): void {
            this.noteToEdit = noteToEdit;
            this.operation = "edit";
        }

        private async deleteNote(noteToDel: NoteInterface): Promise<void> {
            const success = typeof this.onNoteUpdate === "function" ? await this.onNoteDelete!(noteToDel) : true;
            if (!success) return;
            this.removeNoteFromList(noteToDel);
        }

        private async saveEditedNote(newNote: NoteInterface): Promise<void> {
            this.noteToEdit = {id: 0, content: ''};
            this.operation = "view";
            const success = typeof this.onNoteUpdate === "function" ? await this.onNoteUpdate!(newNote) : true;
            if (!success) return;
            this.updateNoteInList(newNote);
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
            this.noteToEdit = {id: 0, content: ''};
            this.operation = "view";
        }
    }
</script>

<style lang="less">
	.title-date {
		font-weight: bold;
	}
</style>
