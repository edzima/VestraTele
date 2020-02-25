<template>
  <div class="calendar">
    <div class="additionalControls">
      <div class="leftControls">
        <button
        :style="buttonStyles[type.id-1]"
        :class="isFilterActive(type.id) ? '' : 'disabled'"
        @click="toggleFilter(type.id)"
        v-for="type in eventTypes"
        :key="type.id">{{type.name}}</button>
      </div>
      <div class="rightControls">

      </div>
    </div>
    <FullCalendar
      ref="fullCalendar"
      :defaultView="calendar.defaultView"
      :header="calendar.header"
      :plugins="calendar.plugins"
      :events="calendar.visibleEvents"
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
    fetchedEvents: [],
    visibleEvents: []
  };

  private activeFilters: Array<any> = [1, 2, 3, 4]
  private eventTypes: Array<any> = [{ id: 1, name: 'umówiony' }, { id: 2, name: 'umowa' }, { id: 3, name: 'niepodpisany' }, { id: 4, name: 'wysłane dokumenty' }]
  // umówiony, umowa, niepodpisany, wysłany
  // private buttonStyles: Array<any> = ['blue', 'green', 'red', 'yellow']
  private buttonStyles: Array<any> = [
    { backgroundColor: 'blue', color: 'white' },
    { backgroundColor: 'green', color: 'white' },
    { backgroundColor: 'red', color: 'white' },
    { backgroundColor: 'yellow', color: 'black' }
  ]

  private toggleFilter (filterId: number): void {
    if (this.activeFilters.includes(filterId)) {
      const filtered = this.activeFilters.filter(id => id !== filterId)
      this.activeFilters = filtered
    } else {
      this.activeFilters.push(filterId)
    }
    this.calendar.visibleEvents = this.calendar.fetchedEvents.filter(event => this.activeFilters.includes(event.typeId))
  }

  private isFilterActive (filterId: number): boolean {
    return this.activeFilters.includes(filterId)
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
    const dateFrom: string = Calendar.formatDate(eventCard.start.toISOString(), parseSettings)
    const dateTo: string = eventCard.end ? Calendar.formatDate(eventCard.end.toISOString(), parseSettings) : ''
    const eventId: number = eventCard.id

    const params: any = new URLSearchParams()
    params.append('id', eventId)
    params.append('date_at', dateFrom)
    params.append('date_end_at', dateTo)
    axios.post(this.URLUpdate, params)
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
      start: eventCard.date_at,
      typeId: 1
    }))

    console.log(fullCalendarEvents)

    this.calendar.fetchedEvents = fullCalendarEvents
    this.calendar.visibleEvents = fullCalendarEvents
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
.calendar{
  .additionalControls{
    .leftControls{
      width: 45vw;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      button{
        height: 5vh;
        width: 10vw;
        border-radius: 10px;
        font-size: 15px;
        color: white;
        border: none;
        margin: 0 auto;
        box-shadow: 0 4px 5px rgba(0,0,0,0.1);
        &.disabled{
          // background-color: #fff !important;
          // color: black !important;
          opacity: 0.4;
        }
      }
    }
  }
}
</style>
