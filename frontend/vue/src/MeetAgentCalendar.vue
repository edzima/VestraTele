<template>
	<div class="calendar">
		<Filters :activeTypes="activeTypes" :eventTypes="eventTypes" @toggleFilter="toggleFilter"/>
		<Calendar
				:activeTypes="activeTypes"
				:agentId="agentId"
				:allEvents="allEvents"
				:allNotes="allNotes"
				:allowUpdate="allowUpdate"
				:eventTypes="eventTypes"
				:isTitlesHidden="isSmallDevice"
				@addNote="addNote"
				@dateClick="addEvent"
				@deleteNote="deleteNote"
				@editNoteText="editNoteText"
				@eventDoubleClick="openEventInspect"
				@eventEdit="updateDates"
				@loadMonth="fetchAndCacheMonth"
		/>
	</div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Calendar from '@/components/Calendar.vue';
    import Filters from '@/components/Filters.vue';
    import {dateToW3C, getFirstOfMonth, getLastOfMonth} from '@/helpers/dateHelper.ts';
    import {MeetingType} from '@/types/MeetingType.ts';
    import {CalendarNote} from '@/types/CalendarNote.ts';
    import {CalendarEvent} from '@/types/CalendarEvent.ts';

    type MonthCacheInfo = {
        monthID: number;
        year: number;
    };

    @Component({
        components: {
            Calendar,
            Filters
        }
    })
    export default class App extends Vue {
        @Prop({
            default: () => true // to change
        })
        private allowUpdate!: boolean; // allow user to edit events

        @Prop({
            required: true
        })
        private agentId!: number;

        @Prop({
            default: () => 'meet-calendar/list'
        })
        private URLGetEvents!: string;


        @Prop({
            default: () => 'meet-calendar/update'
        })
        private URLUpdateEvent!: string;


        @Prop({
            default: () => 'meet/create'
        })
        private URLAddEvent!: string;


        @Prop({
            default: () => 'meet/view'
        })
        private URLInspectEvent!: string;


        @Prop({
            default: () => 'calendar-note/list'
        })
        private URLGetNotes!: string;


        @Prop({
            default: () => 'calendar-note/add'
        })
        private URLNewNote!: string;


        @Prop({
            default: () => 'calendar-note/update'
        })
        private URLUpdateNote!: string;

        @Prop({
            default: () => 'calendar-note/delete'
        })
        private URLDeleteNote!: string;


        @Prop({
            default: () => 600 // in px
        })
        private EventTitleMinRes!: number;

        private activeTypes: number[] = [1, 2, 3, 4];
        private eventTypes: MeetingType[] = [
            {id: 1, name: 'umówiony', className: 'blue'},
            {id: 2, name: 'umowa', className: 'green'},
            {id: 3, name: 'niepodpisany', className: 'red'},
            {id: 4, name: 'wysłane dokumenty', className: 'yellow'}
        ];
        private allEvents: CalendarEvent[] = [];
        private allNotes: CalendarNote[] = [];
        private fetchedMonths: MonthCacheInfo[] = [];
        private isSmallDevice = false;

        created() {
            this.configSmallDevice();
            this.setAxiosErrorHandler();
        }

        private openEventInspect(id: number): void {
            window.open(`${this.URLInspectEvent}?id=${id}`);
        }

        private async deleteNote(noteID: number): Promise<void> {
            const params: URLSearchParams = new URLSearchParams();
            params.append('id', String(noteID));
            params.append('agent_id', String(this.agentId));
            const res = await this.axios.post(this.URLDeleteNote, params);
            if (res.status !== 200) return;
            this.allNotes = this.allNotes.filter(note => note.id !== noteID);
        }

        private toggleFilter(filterId: number): void {
            if (this.activeTypes.includes(filterId)) {
                this.activeTypes = this.activeTypes.filter(id => id !== filterId);
            } else {
                this.activeTypes.push(filterId);
            }
        }

        private cachingMonth: number | null = null;

        private async fetchAndCacheMonth(monthDate: Date): Promise<void> {
            if (this.cachingMonth === monthDate.getMonth() || this.isMonthInCache(monthDate)) return;
            this.cachingMonth = monthDate.getMonth();
            await this.fetchMonthEvents(monthDate);
            await this.fetchMonthNotes(monthDate);
            this.setMonthCached(monthDate);
            this.cachingMonth = null;
        }

        private isMonthInCache(monthDate: Date): boolean {
            return this.fetchedMonths.some(
                ftchMonth =>
                    ftchMonth.monthID === monthDate.getMonth() &&
                    ftchMonth.year === monthDate.getFullYear()
            );
        }

        private setMonthCached(monthDate: Date) {
            this.fetchedMonths.push({
                monthID: monthDate.getMonth(),
                year: monthDate.getFullYear()
            });
        }

        private async fetchMonthEvents(
            monthDate: Date
        ): Promise<void> {
            const startDate: Date = getFirstOfMonth(monthDate);
            const endDate: Date = getLastOfMonth(monthDate);
            const startDateFormatted: string = dateToW3C(startDate);
            const endDateFormatted: string = dateToW3C(endDate);
            const res = await this.axios.get(this.URLGetEvents, {
                params: {
                    agentId: this.agentId,
                    dateFrom: startDateFormatted,
                    dateTo: endDateFormatted
                }
            });
            const mapped: CalendarEvent[] = res.data.data.map(eventCard => ({
                id: eventCard.id,
                title: eventCard.client,
                start: eventCard.date_at,
                end: eventCard.date_end_at,
                phone: eventCard.phone,
                address: eventCard.street,
                city: eventCard.city,
                client: eventCard.client,
                typeId: eventCard.typeId
            }));
            this.allEvents.push(...mapped);
        }

        private async fetchMonthNotes(monthDate: Date): Promise<void> {
            const startDate: Date = getFirstOfMonth(monthDate);
            const endDate: Date = getLastOfMonth(monthDate);
            const res = await this.axios.get(this.URLGetNotes, {
                params: {
                    agentId: this.agentId,
                    dateFrom: startDate,
                    dateTo: endDate
                }
            });
            const notes = res.data.data.map(eventCard => ({
                id: eventCard.id,
                title: eventCard.content,
                start: eventCard.start_at,
                end: eventCard.end_at,
                allDay: true
            }));
            this.allNotes.push(...notes);
        }

        private async addNote(noteText: string, day: Date): Promise<void> {
            const formatted = dateToW3C(day);

            const params: URLSearchParams = new URLSearchParams();
            params.append('date', formatted);
            params.append('agent_id', String(this.agentId));
            params.append('news', noteText);
            const res = await this.axios.post(this.URLNewNote, params);
            if (res.status !== 200) return;
            this.allNotes.push({
                title: noteText,
                id: res.data.id,
                start: dateToW3C(day),
                allDay: true
            });
        }

        private addEvent(date: Date): void {
            window.open(`${this.URLAddEvent}?date=${dateToW3C(date)}`);
        }

        private async editNoteText(noteID: number, text: string): Promise<void> {
            const params: URLSearchParams = new URLSearchParams();
            params.append('news', text);
            params.append('agent_id', String(this.agentId));
            params.append('id', String(noteID));

            const res = await this.axios.post(this.URLUpdateNote, params);
            if (res.status !== 200) return;
            if (res.data.success === false) return;
            this.allNotes = this.allNotes.map(note => {
                if (note.id === noteID) {
                    return {
                        ...note,
                        title: text
                    };
                }
                return note;
            });
        }

        private async updateDates(e: any): Promise<void> {
            const isNote: boolean = Boolean(e.event.allDay);
            const dateFrom: string = dateToW3C(e.event.start);
            const dateTo: string = dateToW3C(e.event.end);

            const params: URLSearchParams = new URLSearchParams();
            params.append('id', String(e.event.id));
            params.append(isNote ? 'start' : 'date_at', String(dateFrom));
            params.append(isNote ? 'end' : 'date_end_at', String(dateTo));
            const res = await this.axios.post(isNote ? this.URLUpdateNote : this.URLUpdateEvent, params);
            if (res.status !== 200) return e.revert();
            if (res.data.success === false) return e.revert();
            if (isNote) {
                this.updateCachedNotes(e);
            }
        }

        private async updateCachedNotes(e: any): Promise<void> {
            this.allNotes = this.allNotes.map(note => {
                if (note.id === e.event.id) {
                    return {
                        ...note,
                        start: e.event.start,
                        end: e.event.end
                    };
                }
                return note;
            });
        }

        private configSmallDevice(): void {
            if (window.innerWidth < this.EventTitleMinRes) {
                this.isSmallDevice = true;
            }
        }

        private handleAxiosError(): void {
            this.$swal({
                icon: 'error',
                title: 'Ups...',
                text: 'coś poszło nie tak!'
            });
        }

        private setAxiosErrorHandler(): void {
            this.axios.interceptors.response.use(res => {
                // is ok
                return res;
            }, () => {
                // error
                this.handleAxiosError();
                return {};
            });
        }
    }
</script>

<style lang='less' scoped>
</style>
