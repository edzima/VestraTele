<template>
	<ul class="list-group">
		<Note :key="note.id" :noteInfo="note" class="list-group-item" v-for="note in notes">
			<template scope="props">
				<slot :note="props.note"/>
			</template>
		</Note>
	</ul>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Note, {NoteInterface} from "@/components/Note.vue";

    @Component({
        components: {Note}
    })
    export default class NoteList extends Vue {
        @Prop() private notes!: NoteInterface[];
    }
</script>

<style lang="less" scoped>
</style>
