<template>
	<li class="note">
		<p>
			{{noteInfo.content}}
		</p>
		<div class="note-controls">
			<EditActions @deleteClick="deleteClick" @editClick="editClick"/>
		</div>
	</li>
</template>


<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import EditActions from "@/components/EditActions.vue";

    export interface NoteInterface {
        content: string;
        id: number;
    }

    @Component({
        components: {EditActions}
    })
    export default class EditableNote extends Vue {
        @Prop() noteInfo!: NoteInterface;

        private editClick(): void {
            this.$emit('editClick', this.noteInfo);
        }

        private deleteClick(): void {
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

			/deep/ .edit-actions {
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
