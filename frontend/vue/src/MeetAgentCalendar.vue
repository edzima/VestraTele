<template>
  <div class="calendar">
    <Filters
    :eventTypes="eventTypes"
    :activeTypes="activeTypes"
    @toggleFilter="toggleFilter"
    />
    <Calendar
    :eventTypes="eventTypes"
    :activeTypes="activeTypes"
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
import { getFirstOfMonth, getLastOfMonth, dateToW3C } from '@/helpers/dateHelper.ts'
import { MeetingType } from '@/types/MeetingType.ts'
import { CalendarNote } from '@/types/CalendarNote.ts'
import { CalendarEvent } from '@/types/CalendarEvent.ts'

import axios from 'axios'

type MonthCacheInfo ={
  monthID: number;
  year: number;
}

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

  private activeTypes: number[] = [1, 2, 3, 4];
  private eventTypes: MeetingType[] = [{ id: 1, name: 'umówiony', className: 'blue' }, { id: 2, name: 'umowa', className: 'green' }, { id: 3, name: 'niepodpisany', className: 'red' }, { id: 4, name: 'wysłane dokumenty', className: 'yellow' }]
  private allEvents: CalendarEvent[] = []
  private allNotes: CalendarNote[] = [
    {
      id: 1,
      title: 'eee',
      start: '2020-02-29 00:00:00',
      end: '2020-03-01 00:00:00',
      allDay: true
    }
  ]

  private fetchedMonths: MonthCacheInfo[] = [];

  private openEventInspect (id: number): void {
    window.open(`${this.URLInspectEvent}?id=${id}`)
  }

  private deleteNote (noteID: number): void {
    // AXIOS
    this.allNotes = this.allNotes.filter(note => note.id !== noteID)
  }

  private toggleFilter (filterId: number): void {
    if (this.activeTypes.includes(filterId)) {
      this.activeTypes = this.activeTypes.filter(id => id !== filterId)
    } else {
      this.activeTypes.push(filterId)
    }
  }

  private async fetchAndCacheMonth (monthDate: Date): Promise<void> {
    const monthExsist: boolean = this.fetchedMonths.some(ftchMonth => ftchMonth.monthID === monthDate.getMonth() && ftchMonth.year === monthDate.getFullYear())
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
    const startDate: Date = getFirstOfMonth(monthDate)
    const endDate: Date = getLastOfMonth(monthDate)
    const startDateFormatted: string = dateToW3C(startDate)
    const endDateFormatted: string = dateToW3C(endDate)
    const res = await axios.get(this.URLGetEvents, {
      params: {
        agentId: this.agentId,
        dateFrom: startDateFormatted,
        dateTo: endDateFormatted
      }
    })
    return res.data.data.map((eventCard) => ({
      // @TODO add here type from EventApiType iplementation
      id: eventCard.id,
      title: eventCard.client,
      start: eventCard.date_at,
      end: eventCard.date_end_at,
      phone: eventCard.phone,
      address: eventCard.street,
      city: eventCard.city,
      client: eventCard.client,
      typeId: eventCard.typeId
    }))
  }

  private async fetchMonthNotes (monthDate: Date): Promise<CalendarEvent[]> {
    const startDate: Date = getFirstOfMonth(monthDate)
    const endDate: Date = getLastOfMonth(monthDate)
    const res = await axios.get(this.URLGetNotes, {
      params: {
        agentId: this.agentId,
        dateFrom: startDate,
        dateTo: endDate
      }
    })
    console.log(res)
    return res.data.data.map(eventCard => ({
      id: eventCard.id,
      title: eventCard.client,
      start: eventCard.date_at,
      end: eventCard.date_to,
      allDay: true
    }))
  }

  private addNote (noteText: string, day: Date): void {
    // send data
    // get id from axios
    // const id = axios.res.id
    // agentId: this.agentId,
    //  URLNewNote
    this.allNotes.push({
      title: noteText,
      id: 1,
      start: dateToW3C(day),
      allDay: true
    })
  }

  private addEvent (date: Date): void{
    window.open(`${this.URLAddEvent}?date=${dateToW3C(date)}`)
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
    const dateFrom: string = dateToW3C(e.event.start)
    const dateTo: string = dateToW3C(e.event.end)
    const eventId: number = eventCard.id
    const params: URLSearchParams = new URLSearchParams()

    params.append('id', String(eventId))
    params.append('date_at', String(dateFrom))
    params.append('date_end_at', String(dateTo))
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
