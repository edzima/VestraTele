<template>
	<BootstrapPopup ref="popup">
		<template v-slot:header>
			<button @click.prevent="closeCalendarNotes" aria-label="Close" class="close" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title">Notatki z dnia <span class="text-primary title-date">{{pettyDayDate}}</span></h4>
		</template>
		<template v-slot:body>
			<EditableNotesList :notes="notes" confirmDelete/>
		</template>
		<template v-slot:footer>
		</template>
	</BootstrapPopup>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import BootstrapPopup, {PopupInterface} from "@/components/BootstrapPopup.vue";
    import {DateClickWithDayEvents, EventObject} from "@/types/FullCalendar";
    import EditableNotesList from "@/components/EditableNotesList.vue";
    import {NoteInterface} from "@/components/Note.vue";
    import {prettify} from "@/helpers/dateHelper";

    export interface NotesPopupInterface {
        readonly openCalendarDayNotes: Function;
        readonly closeCalendarNotes: Function;
    }

    @Component({
        components: {EditableNotesList, BootstrapPopup}
    })
    export default class CalendarNotesPopup extends Vue {
        private operation: 'view' | 'edit' = 'view';

        get popup(): PopupInterface {
            return this.$refs.popup;
        }

        private notes: NoteInterface[] = [];
        private pettyDayDate: string = '';

        public openCalendarDayNotes(dayInfo: DateClickWithDayEvents): void {
            this.operation = "view";
            this.notes = this.getNotesFromDayInfo(dayInfo);
            this.pettyDayDate = prettify(dayInfo.dateStr);
            this.popup.show();
        }

        public closeCalendarNotes(): void {
            this.popup.hide();
        }

        private getNotesFromDayInfo(dayInfo: DateClickWithDayEvents): NoteInterface[] {
            console.log(dayInfo.dayEvents);
            return dayInfo.dayEvents.map((event: EventObject) => ({
                content: event.title,
                id: event.id
            }))
        }

    }
</script>

<style lang="less">
	.title-date {
		font-weight: bold;
	}
</style>
