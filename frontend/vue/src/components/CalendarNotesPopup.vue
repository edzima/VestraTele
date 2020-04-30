<template>
	<Popup ref="popup">
		<template v-slot:header>
			<button @click.prevent="hide" aria-label="Close" class="close" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title">Notatki z dnia</h4>
		</template>
		<template v-slot:body>

		</template>
		<template v-slot:footer>

		</template>
	</Popup>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import Popup, {PopupInterface} from "@/components/Popup.vue";
    import {DateClickInfo} from "@/types/FullCalendar";

    export interface NotesPopupInterface {
        readonly openDayNotes: Function;
    }

    @Component({
        components: {Popup}
    })
    export default class CalendarNotesPopup extends Vue {
        private operation: 'view' | 'edit' = 'view';

        get popup(): PopupInterface {
            return this.$refs.popup;
        }

        public openDayNotes(dayInfo: DateClickInfo) {
            this.operation = "view";
        }

    }
</script>

<style lang="less" scoped>
</style>
