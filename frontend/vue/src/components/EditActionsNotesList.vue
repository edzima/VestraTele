<template>
	<NotesList v-bind="$props">
		<template slot-scope="props">
			<div class="note-controls-icons">
				<span @click="editClick(props.note)" aria-hidden="true" class="glyphicon glyphicon-pencil note-contol-icon text-muted"></span>
				<span @click="deleteClick(props.note)" aria-hidden="true" class="glyphicon glyphicon-trash note-contol-icon text-danger"></span>
			</div>
		</template>
	</NotesList>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import {NoteInterface} from "@/components/Note.vue";
    import NotesList from "@/components/NotesList.vue";
    import {noteDelConfirmSwal} from "@/helpers/swalAlertConfigs";

    @Component({
        components: {NotesList}
    })
    export default class EditActionsNoteList extends Vue {
        @Prop() private notes!: NoteInterface[];
        @Prop({default: () => false, type: Boolean}) private confirmDelete!: boolean;

        private async deleteClick(noteToDelete: NoteInterface): Promise<void> {
            if (this.confirmDelete) {
                const confirmed = await this.$swal(noteDelConfirmSwal);
                if (!confirmed.value) return;
            }
            this.$emit('deleteNote', noteToDelete);
        }

        private editClick(noteToDelete: NoteInterface) {
            this.$emit('editNote', noteToDelete);
        }
    }
</script>

<style lang="less">
	.note-controls-icons {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100%;

		.note-contol-icon {
			transform: scale(1.5);
			margin: 0 5px;
			cursor: pointer;
		}
	}
</style>
