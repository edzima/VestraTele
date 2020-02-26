<template>
  <div class="calendar">
    <FullCalendar
      ref="fullCalendar"
      :defaultView="calendar.defaultView"
      :header="calendar.header"
      :plugins="calendar.plugins"
      :events="events"
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
const FullCalendar = require('@fullcalendar/vue').default
const CalendarCore = require('@fullcalendar/core')

@Component({
  components: {
    FullCalendar
  }
})
export default class Calendar extends Vue {
  @Prop({
    required: true
  })
  private allowUpdate!: boolean; // allow user to edit events

  @Prop({
    required: true
  })
  private agentId!: number;

  @Prop({
    required: true
    // -----PARAMS-----
    // id - eventId
    // date_at - date from
    // date_end_at - date to
  })
  private URLUpdate!: string;

  @Prop({
    // -----PARAMS-----
    // dateFrom
    // dateTo
    // agentId
    required: true
  })
  private URLGetEvents!: string;

  @Prop({ required: true })
  private fetchedEvents: Array<any>

  @Prop({ required: true })
  private activeFilters: Array<number>

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
    columnHeaderFormat: { weekday: 'long', day: 'numeric' }
  };

  get events (): Array<any> {
    return this.fetchedEvents.filter(event => this.activeFilters.includes(event.typeId))
  }

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
    const dateFrom: string = CalendarCore.formatDate(
      eventCard.start.toISOString(),
      parseSettings
    )
    const dateTo: string = eventCard.end
      ? CalendarCore.formatDate(eventCard.end.toISOString(), parseSettings)
      : ''
    const eventId: number = eventCard.id

    const params: any = new URLSearchParams()
    params.append('id', eventId)
    params.append('date_at', dateFrom)
    params.append('date_end_at', dateTo)
    axios.post(this.URLUpdate, params)
  }
}
</script>

<style scoped lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
</style>
