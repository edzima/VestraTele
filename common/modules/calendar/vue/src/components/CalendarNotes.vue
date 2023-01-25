<template>
    <div class="calendar-notes">
        <template v-if="isOperation('view')">
            <NotesList
                :notes="localNotes"
                confirmDelete
                editable
                @deleteClick="deleteNote"
                @editClick="editNote"
            />
            <p v-if="!localNotes.length" class="note-placeholder text-primary"> Brak notatek </p>
            <Button class="btn btn-primary" @click="addNote">Dodaj Notatkę</Button>
        </template>
        <template v-if="isOperation('edit')">
            <NoteEditor :note="noteToEdit" auto-focus @discardChanges="discardEditChanges" @saveEditedNote="noteChangesHandler"/>
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
    @Prop() onNoteAdd: Function | undefined;
    @Prop({default: () => []}) notes!: NoteInterface[];
    @Prop({default: () => false}) editable!: boolean;
    @Prop() URLDelete!: string;

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
        this.noteToEdit = {id: null, content: '', update: true, delete: true};
    }

    private async deleteNote(note: NoteInterface): Promise<boolean> {
        if (this.URLDelete) {
            const params: URLSearchParams = new URLSearchParams();
            params.append('id', String(note.id));
            const res = await this.axios.post(this.URLDelete, params);
            if (res.status !== 200) return false;
            this.removeNoteFromList(note);
            this.$emit('deleteNote', note);
            return true;
        }
        return false;

    }

    private noteChangesHandler(note: NoteInterface): void {
        if (note.id) {
            this.saveEditedNote(note);
        } else {
            this.addAndSaveNote(note);
        }
        this.discardEditChanges();
    }

    private async addAndSaveNote(note: NoteInterface): Promise<void> {
        const noteId = typeof this.onNoteAdd === "function" ? await this.onNoteAdd!(note) : true;
        if (!noteId) return;
        this.addNoteToList({id: noteId, content: note.content, update: true, delete: true});
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
            }
            return note;
        })
    }

    private removeNoteFromList(noteToDel: NoteInterface): void {
        this.localNotes = this.localNotes.filter((note: NoteInterface) => note.id !== noteToDel.id);
    }

    private discardEditChanges(): void {
        this.noteToEdit = {id: null, content: '', delete: true, update: true};
        this.operation = "view";
    }
}
</script>

<style lang="less">
.calendar-notes {
    display: flex;
    flex-direction: column;
    min-height: 30vh;

    .note-placeholder {
        font-weight: bold;
        font-size: 25px;
        text-align: center;
    }

    .btn {
        margin: auto 0 0 auto;
        align-self: flex-end;
        justify-self: flex-end;
    }
}
</style>
