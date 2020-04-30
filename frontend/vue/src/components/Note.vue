<template>
	<li class="note">
		<p>
			{{noteInfo.content}}
		</p>
		<div class="note-controls">
			<slot :note="noteInfo"/>
		</div>
	</li>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import {componentHasSlot} from "@/helpers/componentHelper";

    export interface NoteInterface {
        content: string;
        id: number;
    }

    @Component
    export default class Note extends Vue {
        @Prop() noteInfo!: NoteInterface;

        private hasSlot(name: string): boolean {
            return componentHasSlot(this, name);
        }
    }
</script>

<style lang="less" scoped>
	.note {
		display: flex;

		.note-controls {
			justify-self: flex-end;
			margin-left: auto;
		}
	}
</style>
