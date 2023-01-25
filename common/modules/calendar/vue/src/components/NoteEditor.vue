<template>
    <div class="note-edit-container">
        <div class="form-group">
            <label for="note-editor">Treść</label>
            <textarea id="note-editor" ref="noteEditor" v-model.trim="newNote.content" class="form-control" rows="3"></textarea>

            <div class="note-editor-controls">
                <button :class="{disabled: !isNoteEdited}" :disabled="!isNoteEdited" class="btn btn-primary note-edit-controll" type="button" @click="saveEditNote">Zapisz</button>
                <button class="btn btn-danger note-edit-controll" type="button" @click="discardChanges">Anuluj</button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
import {NoteInterface} from "@/components/Note.vue";
import {noteEditConfirmSwal} from "@/helpers/swalAlertConfigs";

@Component
export default class NoteEditor extends Vue {
    @Prop({required: true}) note!: NoteInterface;
    @Prop({type: Boolean, default: () => false}) autoFocus!: boolean;
    @Prop({type: Boolean, default: () => false}) confirmEdit!: boolean;

    private newNote: NoteInterface = {
        id: null,
        content: '',
        update: true,
        delete: true
    };
    private isNoteEdited: boolean = false;

    get noteTextArea(): HTMLElement {
        return (this.$refs.noteEditor as HTMLElement)
    }

    @Watch('newNote', {deep: true})
    onPropertyChanged() {
        this.isNoteEdited = this.newNote.content !== this.note.content;
    }

    mounted(): void {
        if (this.autoFocus) this.noteTextArea.focus();
        this.newNote = {...this.note};
    }

    private async saveEditNote(): Promise<void> {
        if (this.note.id) {
            const confirmed = await this.$swal(noteEditConfirmSwal);
            if (!confirmed.value) return;
        }
        this.$emit('saveEditedNote', this.newNote);
    }

    private discardChanges(): void {
        this.$emit('discardChanges');
    }

}
</script>

<style lang="less">
.note-edit-container {

    .note-editor-controls {
        margin-left: auto;
        margin-right: 0;
        margin-top: 5px;
        width: 100%;
        display: flex;
        justify-content: flex-end;

        .note-edit-controll {
            margin: 0 2px;
        }
    }
}
</style>
