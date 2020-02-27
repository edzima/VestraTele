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
      :datesRender="loadEvents"
      :showNonCurrentDates="false"
      @eventDrop="handleChangeDates"
      @eventClick="inspectEvent"
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
import { dateToW3C, isSameMonth } from '@/helpers/dateHelper.ts'
import axios from 'axios'
const FullCalendar = require('@fullcalendar/vue').default

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

  @Prop({
    required: true
  })
  private URLInspectEvent!: string;

  @Prop({ required: true })
  private allEvents!: Array<any>;

  @Prop({ required: true })
  private activeFilters!: Array<number>;

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
    minTime: '0:00:00',
    maxTime: '24:00:00',
    eventDurationEditable: this.allowUpdate, // allow to extend time
    columnHeaderFormat: { weekday: 'long', day: 'numeric' }
  };

  private eventClick: any = {
    eventClicked: null,
    timeoutId: null
  }

  get events (): Array<any> {
    const filtered = this.allEvents.filter(event =>
      this.activeFilters.includes(event.typeId)
    )
    console.log(filtered)
    return filtered
  }

  private async handleChangeDates (e: any): Promise<void> {
    if (!this.allowUpdate) return // cancel if no permissions
    const eventCard: any = e.event
    console.log(eventCard)

    const dateFrom = dateToW3C(e.event.start)
    const dateTo = dateToW3C(e.event.end)
    console.log(dateFrom)

    const eventId: number = eventCard.id
    const params: any = new URLSearchParams()
    params.append('id', eventId)
    params.append('date_at', dateFrom)
    params.append('date_end_at', dateTo)
    axios.post(this.URLUpdate, params)
  }

  private loadEvents (): void {
    const fullcalendar: any = this.$refs.fullCalendar
    const calApi: any = fullcalendar.getApi()
    console.log(calApi)

    const startDate: Date = calApi.view.activeStart // counting from 0 -> january
    const endDate: Date = calApi.view.activeEnd

    this.$emit('loadMonth', startDate)
    if (!isSameMonth(startDate, endDate)) {
      this.$emit('loadMonth', endDate)
    }
  }

  private inspectEvent (event: any): void{
    if (!this.eventClick.timeoutId) {
      this.eventClick.timeoutId = setTimeout(() => {
      // simple click
        clearTimeout(this.eventClick.timeoutId)
        this.eventClick.timeoutId = null
        this.eventClick.eventClicked = event.el
      }, 250)// tolerance in ms
    } else {
      // double click
      clearTimeout(this.eventClick.timeoutId)
      this.eventClick.timeoutId = null
      this.eventClick.eventClicked = event.el
      console.log(event.el)
      const linkToInspect = `${this.URLInspectEvent}?id=${event.event.id}`
      window.open(linkToInspect)
    }
  }
}
</script>

<style scoped lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
</style>
