<template>
	<NotesList v-bind="$props">
		<template scope="props">
			<EditableNoteListControls :note="props.note" @deleteClick="deleteClick" @editClick="editClick"/>
		</template>
	</NotesList>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import {NoteInterface} from "@/components/Note.vue";
    import NotesList from "@/components/NotesList.vue";
    import EditableNoteListControls from "@/components/EditableNoteListControls.vue";

    @Component({
        components: {EditableNoteListControls, NotesList}
    })
    export default class EditableNoteList extends Vue {
        @Prop() private notes!: NoteInterface[];
        @Prop({default: () => false, type: Boolean}) private confirmDelete!: boolean;

        private async deleteClick(): Promise<void> {
            if (this.confirmDelete) {
                const confirmed = await this.$swal({
                    title: 'Usunąć notatkę?',
                    text: 'Ta operacja nie może zostać cofnięta',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Tak usuń!',
                    confirmButtonColor: '#95a2a9',
                    cancelButtonText: 'Nie, zachowaj!',
                    cancelButtonColor: '#0c9cff',
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                })
                if (!confirmed) return;
            }
            this.$emit('deleteNote');
        }

        private editClick() {

        }
    }
</script>

<style lang="less">
</style>
