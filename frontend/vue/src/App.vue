<template>
  <div class="app">
    <FullCalendar
      ref="fullCalendar"
      defaultView="timeGridWeek"
      :header="{center: 'today prev,next', right: 'dayGridMonth,timeGridWeek,dayGridDay' }"
      :plugins="calendarPlugins"
    />
  </div>
</template>


<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import listWeekPlugin from "@fullcalendar/list";
import timeGridPlugin from "@fullcalendar/timegrid";

export default {
  components: {
    FullCalendar
  },
  props: {
    agentId: {
      type: Number
      // required: true
    },
    cardBaseUrl: {
      type: String
      // required: true
    },
    updateURL: {
      type: String
      // required: true
    },
    getCalendarURL: {
      type: String
      // required: true
    },
    allowUpdate: {
      type: Boolean
    }
  },
  data() {
    return {
      calendarPlugins: [dayGridPlugin, listWeekPlugin, timeGridPlugin]
    };
  },
  methods: {
    changeView(viewType = "listWeek") {
      // "dayGridMonth" - month view
      // "listWeek" - week view
      this.$refs.fullCalendar.getApi().changeView(viewType);
    }
  }
};
</script>



<style scoped lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
</style>