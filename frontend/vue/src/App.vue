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
    />
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import dayGridPlugin from '@fullcalendar/daygrid'
import listWeekPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
// const FullCalendar = require('@fullcalendar/vue').default
const FullCalendar = require('@fullcalendar/vue').default

@Component({
  components: {
    FullCalendar
  }
})
export default class App extends Vue {
  private calendar: any = {
    plugins: [
      dayGridPlugin,
      listWeekPlugin,
      timeGridPlugin,
      interactionPlugin
    ],
    header: {
      left: 'title',
      center: 'today prev,next'
      // right: 'dayGridMonth,timeGridWeek,dayGridDay'
    },
    defaultView: 'timeGridWeek',
    // locale: plLang,
    editable: true,
    droppable: false, // external calendars events
    minTime: '8:00:00',
    maxTime: '24:00:00',
    eventDurationEditable: false, // allow to extend time
    columnHeaderFormat: { weekday: 'long', day: 'numeric' },
    // businessHours: { start: "8:00", end: "20:00" },
    events: []
  }
}
</script>

<style scoped lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
</style>
