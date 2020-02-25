<template>
  <div class="app">
    <FullCalendar
      ref="fullCalendar"
      :defaultView="calendar.defaultView"
      :header="calendar.header"
      :plugins="calendar.plugins"
      :events="calendar.events"
      :locale="calendar.locale"
      :editable="calendar.editable"
      :droppable="calendar.droppable"
      :businessHours="calendar.businessHours"
      :minTime="calendar.minTime"
      :maxTime="calendar.maxTime"
      :eventDurationEditable="calendar.eventDurationEditable"
      :columnHeaderFormat="calendar.columnHeaderFormat"
      @eventDrop="handleChangeDates"
    />
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import dayGridPlugin from '@fullcalendar/daygrid'
import listWeekPlugin from '@fullcalendar/list'
import plLang from '@fullcalendar/core/locales/pl'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import axios from 'axios'
const Calendar = require('@fullcalendar/core')
const FullCalendar = require('@fullcalendar/vue').default

@Component({
  components: {
    FullCalendar
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
  private URLGetCalendar!: string;

  private calendar: any = {
    plugins: [dayGridPlugin, listWeekPlugin, timeGridPlugin, interactionPlugin],
    header: {
      left: 'title',
      center: 'today prev,next',
      right: 'dayGridMonth,timeGridWeek,dayGridDay'
    },
    defaultView: 'timeGridWeek',
    locale: plLang,
    editable: this.allowUpdate, // FLAG FROM PROPS
    droppable: false, // external calendars events
    minTime: '8:00:00',
    maxTime: '24:00:00',
    eventDurationEditable: this.allowUpdate, // allow to extend time
    columnHeaderFormat: { weekday: 'long', day: 'numeric' },
    events: []
  };

  private async handleChangeDates (e: any): Promise<void> {
    if (!this.allowUpdate) return // cancel if no permissions
    const eventCard: any = e.event
    console.log(eventCard)
    const parseSettings = {
      month: 'numeric',
      year: 'numeric',
      day: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      hour12: false,
      meridiem: false,
      separator: '-'
    }
    const dateFrom: string = Calendar.formatDate(eventCard.start.toISOString(), parseSettings)
    const dateTo: string = eventCard.end ? Calendar.formatDate(eventCard.end.toISOString(), parseSettings) : ''
    const eventId: number = eventCard.id

    const params: any = new URLSearchParams()
    params.append('id', eventId)
    params.append('date_at', dateFrom)
    params.append('date_end_at', dateTo)

    const newEventProperties = {
      id: eventId,
      date_at: dateFrom,
      date_end_at: dateTo
    }

    axios.post(this.URLUpdate, newEventProperties)
  }

  private async fetchEvents (): Promise<void> {
    const res = await axios.get(this.URLGetCalendar, {
      params: {
        agentId: this.agentId
      }
    })
    const events = res.data.data
    const fullCalendarEvents = events.map(eventCard => ({
      id: eventCard.id,
      title: eventCard.client,
      start: eventCard.date_at
    }))
    console.log(fullCalendarEvents)

    this.calendar.events = fullCalendarEvents
  }

  created () {
    this.fetchEvents()
  }
}
</script>

<style scoped lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
</style>
