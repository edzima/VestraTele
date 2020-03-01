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
    @loadMonth="fetchAndCacheMonth"
    @deleteNote="deleteNote"
    @addNote="addNote"
    @eventEdit="handleChangeDates"
    @eventDoubleClick="openEventInspect"
    @editNoteText="editNoteText"
    @dateClick="addEvent"
    />
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import Calendar from '@/components/Callendar.vue'
import Filters from '@/components/Filters.vue'
import { CalendarEvent } from '@/types/calendarEventType'
import { getFirstOfMonth, getLastOfMonth, dateToW3C } from '@/helpers/dateHelper.ts'

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
  private URLUpdateEvent!: string;

  @Prop({
    default: () => '/updateNote',
    required: false
    // -----PARAMS-----
    // id - eventId
    // date_at - date from
    // date_end_at - date to
  })
  private URLUpdateNote!: string;

  @Prop({
    default: () => '/update',
    required: false
    // -----PARAMS-----
    // id - eventId
    // date_at - date from
    // date_end_at - date to
  })
  private URLNewNote!: string;

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
    default: () => '/list',
    // -----PARAMS-----
    // dateFrom
    // dateTo
    // agentId
    required: false
  })
  private URLGetNotes!: string;

  @Prop({
    default: () => 'http://google.com',
    // -----PARAMS-----
    // dateFrom
    // dateTo
    // agentId
    required: false
  })
  private URLAddEvent!: string;

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
  private allNotes: Array<any> = [{ id: 1, date: '2020-02-29 00:00:00', title: 'lorem ipsum dolor sir amet und dad ewe ' }, { id: 2, date: '2020-02-28 00:00:00', title: 'lorem ipsum' }, { id: 3, date: '2020-02-29 00:00:00', title: 'lorem ipsum' }, { id: 4, date: '2020-02-29 00:00:00', title: 'lorem ipsum' }]
  private fetchedMonths: Array<{monthID: number; year: number}> = [];

  private openEventInspect (id: number) {
    const linkToInspect = `${this.URLInspectEvent}?id=${id}`
    window.open(linkToInspect)
  }

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
    const fetchedMonthEvents = await this.fetchMonthEvents(monthDate)
    const fetchedMonthNotes = await this.fetchMonthNotes(monthDate)
    this.fetchedMonths.push({
      monthID: monthDate.getMonth(),
      year: monthDate.getFullYear()
    })
    // this.allNotes.push(...fetchedMonthNotes)
    this.allEvents.push(...fetchedMonthEvents)
  }

  private async fetchMonthEvents (monthDate: Date): Promise<Array<CalendarEvent>> {
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
      client: 'Jan Kowal',
      typeId: 2
    }))
  }

  private async fetchMonthNotes (monthDate: Date): Promise<Array<CalendarEvent>> {
    const startDate: string = getFirstOfMonth(monthDate)
    const endDate: string = getLastOfMonth(monthDate)
    const res = await axios.get(this.URLGetNotes, {
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
      start: eventCard.date_at
    }))
  }

  private addNote (noteText: string, day: Date): void {
    // send data
    // get id from axios
    // const id = axios.res.id
    //  URLNewNote
    this.allNotes.push({
      title: noteText,
      id: 1,
      date: day
    })
  }

  private addEvent (date: Date): void{
    const linkToInspect = `${this.URLAddEvent}?date=${dateToW3C(date)}`
    window.open(linkToInspect)
  }

  private editNoteText (noteID: number, text: string): void{
    this.allNotes = this.allNotes.map(note => {
      if (note.id === noteID) {
        return {
          ...note,
          title: text
        }
      }
      return note
    })
  }

  private async handleChangeDates (e: any): Promise<void> {
    const eventCard: any = e.event

    // if there is no oldEvent its just a time change
    if (e.oldEvent) {
    // prevent draging notes to normal events and vice-versa
      if (eventCard.allDay !== e.oldEvent.allDay) return e.revert()
    }
    const dateFrom = dateToW3C(e.event.start)
    const dateTo = dateToW3C(e.event.end)
    const eventId: number = eventCard.id
    const params: any = new URLSearchParams()

    params.append('id', eventId)
    params.append('date_at', dateFrom)
    params.append('date_end_at', dateTo)
    if (eventCard.allDay) {
      // handle edit dates and time of note
      axios.post(this.URLUpdateNote, params)
    } else {
      // event
      axios.post(this.URLUpdateEvent, params)
    }
  }
}
</script>

<style scoped lang='less'>

</style>
