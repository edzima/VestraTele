<template>
	<div class="edit-actions">
		<span @click="editClick" aria-hidden="true" class="glyphicon glyphicon-pencil note-contol-icon text-muted"></span>
		<span @click="deleteClick" aria-hidden="true" class="glyphicon glyphicon-trash note-contol-icon text-danger"></span>
	</div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';

    @Component
    export default class EditActions extends Vue {

        private async deleteClick(): Promise<void> {
            this.$emit('deleteClick');
        }

        private editClick() {
            this.$emit('editClick');
        }
    }
</script>

<style lang="less">

</style>
