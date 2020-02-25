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
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import listWeekPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";

import timeGridPlugin from "@fullcalendar/timegrid";
import plLang from "@fullcalendar/core/locales/pl";
import axios from "axios";

export default {
  components: {
    FullCalendar
  },
  props: {
    agentId: {
      type: Number,
      // required: true
      default: () => 21
    },
    cardBaseUrl: {
      type: String
      // required: true
    },
    updateURL: {
      // meet-calendar/url/?id=&date
      type: String,
      // https://test.vestra.hekko24.pl/meet-calendar/url/id=  &date=
      // required: true
      default: () => "/url"
    },
    getCalendarURL: {
      type: String,
      // https://test.vestra.hekko24.pl/meet-calendar/list?agentId=21
      default: () => "/list"
      // required: true
    },
    allowUpdate: {
      type: Boolean
    }
  },
  data() {
    return {
      calendar: {
        plugins: [
          dayGridPlugin,
          listWeekPlugin,
          timeGridPlugin,
          interactionPlugin
        ],
        header: {
          left: "title",
          center: "today prev,next",
          right: "dayGridMonth,timeGridWeek,dayGridDay"
        },
        defaultView: "timeGridWeek",
        locale: plLang,
        editable: true,
        droppable: false, //external calendars events
        minTime: "8:00:00",
        maxTime: "24:00:00",
        eventDurationEditable: false, //allow to extend time
        columnHeaderFormat: { weekday: "long", day: "numeric" },
        // businessHours: { start: "8:00", end: "20:00" },
        events: []
      }
    };
  },
  methods: {
    async fetchEvents() {
      const res = await axios.get(this.getCalendarURL, {
        params: {
          agentId: this.agentId
        }
      });
      const events = res.data.data;
      const fullCalendarEvents = events.map(event => ({
        id: event.id,
        title: event.client,
        start: event.date_at
      }));
      this.calendar.events = fullCalendarEvents;
    },
    handleChangeDates(e) {
      const { event } = e;
      console.log(event);
      axios.post(this.updateURL, {
        id: event.id,
        date: event.start.toISOString()
      });
    }
  },
  created() {
    this.fetchEvents();
  }
};
</script>



<style scoped lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
</style>