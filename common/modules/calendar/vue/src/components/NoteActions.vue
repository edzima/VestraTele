<template>
    <div class="edit-actions">
        <span v-if="withUpdate" aria-hidden="true" class="glyphicon glyphicon-pencil note-contol-icon text-muted" @click="editClick"></span>
        <span v-if="withDelete" aria-hidden="true" class="glyphicon glyphicon-trash note-contol-icon text-danger" @click="deleteClick"></span>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component
export default class NoteActions extends Vue {

    @Prop({default: () => true}) withUpdate !: boolean;
    @Prop({default: () => true}) withDelete !: boolean;


    private deleteClick() {
        this.$emit('deleteClick');
    }

    private editClick() {
        this.$emit('editClick');
    }
}
</script>


