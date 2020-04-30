<template>
	<div class="note-controls-icons">
		<span @click="editClick" aria-hidden="true" class="glyphicon glyphicon-pencil note-contol-icon text-muted"></span>
		<span @click="deleteClick" aria-hidden="true" class="glyphicon glyphicon-trash note-contol-icon text-danger"></span>
	</div>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import NotesList from "@/components/NotesList.vue";
    import {NoteInterface} from "@/components/Note.vue";

    @Component({
        components: {NotesList}
    })
    export default class EditableNoteListControls extends Vue {
        @Prop() private note!: NoteInterface;

        private editClick() {
            console.log(this.note)
            this.$emit('editClick')
        }

        private deleteClick() {
            this.$emit('deleteClick')
        }

    }
</script>
<style lang="less">
	.note-controls-icons {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100%;

	}
</style>
