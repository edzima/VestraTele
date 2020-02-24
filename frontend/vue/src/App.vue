<template>
  <div class="app">
    <FullCalendar
      ref="fullCalendar"
      :defaultView="calendar.defaultView"
      :header="calendar.header"
      :plugins="calendar.plugins"
      :events="calendar.events"
      :locale="calendar.locale"
    />
  </div>
</template>


<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import listWeekPlugin from "@fullcalendar/list";
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
      type: String
      // required: true
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
        plugins: [dayGridPlugin, listWeekPlugin, timeGridPlugin],
        header: {
          left: "title",
          center: "today prev,next",
          right: "dayGridMonth,timeGridWeek,dayGridDay"
        },
        defaultView: "timeGridWeek",
        locale: plLang,
        events: []
      }
    };
  },
  methods: {
    async fetchEvents() {
      const res = await axios.get(this.getCalendarURL, {
        headers: {
          "Access-Control-Allow-Origin": "*",
          "Content-Type": "application/json"
        },
        params: {
          agentId: this.agentId
        }
      });
      const events = res.data.data;
      console.log(events);
      const fullCalendarEvents = events.map(event => ({
        id: event.id,
        title: event.client,
        start: event.date_at
      }));
      this.calendar.events = fullCalendarEvents;
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