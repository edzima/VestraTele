<template>
    <ul class="list-group">
        <template v-for="note in notes">
            <Note
                :key="note.id"
                :editable="editable"
                :noteInfo="note"
                class="list-group-item"
                @deleteClick="deleteClick"
                @editClick="editClick"
            />
        </template>
    </ul>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import Note, {NoteInterface} from "@/components/Note.vue";
import {noteDelConfirmSwal} from "@/helpers/swalAlertConfigs";

@Component({
    components: {Note}
})

export default class NoteList extends Vue {
    @Prop() private notes!: NoteInterface[];
    @Prop({default: () => false, type: Boolean}) private editable!: boolean;
    @Prop({default: () => false, type: Boolean}) private confirmDelete!: boolean;


    private editClick(noteInfo: NoteInterface): void {
        this.$emit('editClick', noteInfo);
    }

    private async deleteClick(noteInfo: NoteInterface): Promise<void> {
        if (this.confirmDelete) {
            const confirmed = await this.$swal(noteDelConfirmSwal);
            if (!confirmed.value) return;
        }
        this.$emit('deleteClick', noteInfo);
    }
}
</script>

<style lang="less" scoped>
</style>
