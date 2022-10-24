<template>
    <li class="note">
        <p>
            {{ noteInfo.content }}
        </p>
        <span class="note-controls">
			<NoteActions
                :withDelete="noteInfo.delete"
                :withUpdate="noteInfo.update"
                @deleteClick="onDelete"
                @editClick="editClick"
            />
		</span>
    </li>
</template>


<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import NoteActions from "@/components/NoteActions.vue";

export interface NoteInterface {
    content: string,
    id: number | null,
    update: boolean,
    delete: boolean
}

@Component({
    components: {NoteActions}
})
export default class Note extends Vue {
    @Prop() noteInfo!: NoteInterface;
    @Prop() withUpdate!: boolean;
    @Prop() withDelete!: boolean;


    private editClick(): void {
        this.$emit('editClick', this.noteInfo);
    }

    private onDelete(): void {
        this.$emit('deleteClick', this.noteInfo);
    }
}
</script>

<style lang="less" scoped>
.note {
    display: flex;

    .note-controls {
        justify-self: flex-end;
        margin-left: auto;

        :deep(.edit-actions) {
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
    }
}
</style>
