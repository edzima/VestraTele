<template>
  <div class="calendar">
    <Filters
    :eventTypes="eventTypes"
    :activeFilters="activeFilters"
    @toggleFilter="toggleFilter"
    />
    <Calendar
    :eventTypes="eventTypes"
    :activeFilters="activeFilters"
    :allEvents="allEvents"
    :allNotes="allNotes"
    :allowUpdate="allowUpdate"
    :agentId="agentId"
    :URLUpdate="URLUpdate"
    :URLGetEvents="URLGetEvents"
    :URLInspectEvent="URLInspectEvent"
    @loadMonth="fetchAndCacheMonth"
    @deleteNote="deleteNote"
    />
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import Calendar from '@/components/Callendar.vue'
import Filters from '@/components/Filters.vue'
import { CalendarEvent } from '@/types/calendarEventType'
import { getFirstOfMonth, getLastOfMonth } from '@/helpers/dateHelper.ts'

import axios from 'axios'
@Component({
  components: {
    Calendar,
    Filters
  }
})
export default class App extends Vue {
  @Prop({
    default: () => true,
    required: false
  })
  private allowUpdate!: boolean; // allow user to edit events

  @Prop({
    default: () => 21,
    required: false
  })
  private agentId!: number;

  @Prop({
    default: () => '/update',
    required: false
    // -----PARAMS-----
    // id - eventId
    // date_at - date from
    // date_end_at - date to
  })
  private URLUpdate!: string;

  @Prop({
    default: () => '/list',
    // -----PARAMS-----
    // dateFrom
    // dateTo
    // agentId
    required: false
  })
  private URLGetEvents!: string;

  @Prop({
    default: () => 'http://google.com',
    // -----PARAMS-----
    // dateFrom
    // dateTo
    // agentId
    required: false
  })
  private URLInspectEvent!: string;

  private activeFilters: Array<number> = [1, 2, 3, 4];
  private eventTypes: Array<any> = [{ id: 1, name: 'umówiony', className: 'blue' }, { id: 2, name: 'umowa', className: 'green' }, { id: 3, name: 'niepodpisany', className: 'red' }, { id: 4, name: 'wysłane dokumenty', className: 'yellow' }]
  private allEvents: Array<CalendarEvent> = []
  private allNotes: Array<any> = [{ id: 1, date: '2020-02-29 00:00:00', text: 'lorem ipsum' }, { id: 2, date: '2020-02-28 00:00:00', text: 'lorem ipsum' }, { id: 3, date: '2020-02-29 00:00:00', text: 'lorem ipsum' }, { id: 4, date: '2020-02-29 00:00:00', text: 'lorem ipsum' }]
  private fetchedMonths: Array<{monthID: number; year: number}> = [];

  private deleteNote (noteID: number) {
    // AXIOS
    this.allNotes = this.allNotes.filter(note => note.id !== noteID)
  }

  private toggleFilter (filterId: number): void {
    if (this.activeFilters.includes(filterId)) {
      const filtered = this.activeFilters.filter(id => id !== filterId)
      this.activeFilters = filtered
    } else {
      this.activeFilters.push(filterId)
    }
  }

  private async fetchAndCacheMonth (monthDate: Date): Promise<void> {
    const monthExsist = this.fetchedMonths.find(ftchMonth => ftchMonth.monthID === monthDate.getMonth() && ftchMonth.year === monthDate.getFullYear())
    if (monthExsist) return
    const fetchedMonth = await this.fetchMonth(monthDate)
    this.fetchedMonths.push({
      monthID: monthDate.getMonth(),
      year: monthDate.getFullYear()
    })

    this.allEvents.push(...fetchedMonth)
  }

  private async fetchMonth (monthDate: Date): Promise<Array<CalendarEvent>> {
    const startDate: string = getFirstOfMonth(monthDate)
    const endDate: string = getLastOfMonth(monthDate)
    const res = await axios.get(this.URLGetEvents, {
      params: {
        agentId: this.agentId,
        dateFrom: startDate,
        dateTo: endDate
      }
    })
    const events = res.data.data
    return events.map(eventCard => ({
      id: eventCard.id,
      title: eventCard.client,
      start: eventCard.date_at,
      end: eventCard.date_end_at,
      phone: '123 123 123 ',
      address: 'Wolska 19',
      city: 'Warszawa',
      typeId: 2
    }))
  }

  created () {
    // this.fetchAndCacheMonth()
  }
}
</script>

<style scoped lang='less'>

</style>
