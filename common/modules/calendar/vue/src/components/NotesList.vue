<template>
	<ul class="list-group">
		<template v-for="note in notes">
			<Component :is="noteComponent" :key="note.id" :noteInfo="note" @deleteClick="deleteClick" @editClick="editClick" class="list-group-item"/>
		</template>
	</ul>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import Note, {NoteInterface} from "@/components/Note.vue";
import {VueConstructor} from "vue";
import EditableNote from "@/components/EditableNote.vue";
import EditActions from "@/components/EditActions.vue";
import {noteDelConfirmSwal} from "@/helpers/swalAlertConfigs";

@Component({
    components: {Note, EditableNote, EditActions}
})

export default class NoteList extends Vue {
    @Prop() private notes!: NoteInterface[];
    @Prop({default: () => false, type: Boolean}) private editable!: boolean;
    @Prop({default: () => false, type: Boolean}) private confirmDelete!: boolean;

    get noteComponent(): VueConstructor<Vue> {
        return this.editable ? EditableNote : Note;
    }

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
