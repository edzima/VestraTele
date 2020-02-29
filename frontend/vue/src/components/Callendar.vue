<template>
  <div class="calendar">
    <NotesPopup
      :date="addNoteDate"
      :allNotes="allNotes"
      @close="closePopup"
      @deleteNote="deleteNote"
    />
    <ToolTip
      :calendarEvent="toolTip.calendarEvent"
      :element="toolTip.element"
      :isVisible="toolTip.isVisible"
    />
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
      :eventTimeFormat="calendar.eventTimeFormat"
      :nowIndicator="calendar.nowIndicator"
      @eventDrop="handleChangeDates"
      @eventResize="handleChangeDates"
      @eventClick="inspectEvent"
      @dateClick="handleAddNote"
      @eventMouseEnter="openTooltip"
      @eventMouseLeave="closeTooltip"
      @eventRender="editEventHtml"
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
import NotesPopup from './NotesPopup.vue'
import ToolTip from './ToolTip.vue'
import axios from 'axios'
const FullCalendar = require('@fullcalendar/vue').default

@Component({
  components: {
    FullCalendar,
    NotesPopup,
    ToolTip
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
    required: false
  })
  private eventTypes!: Array<any>;

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

  @Prop({})
  private allNotes!: Array<any>;

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
    nowIndicator: true, // red line with current time
    eventTimeFormat: {
      // like '14:30:00'
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    },
    columnHeaderFormat: {
      weekday: 'short',
      month: 'numeric',
      day: 'numeric',
      omitCommas: true
    }
  };

  private deleteNote (noteID: number) {
    // handle delete
    this.$emit('deleteNote', noteID)
  }

  private eventClick: any = {
    eventClicked: null,
    timeoutId: null
  };

  private addNoteDate: any = null;

  private toolTip: any = {
    isVisible: false,
    element: null,
    calendarEvent: null
  };

  private closePopup () {
    this.addNoteDate = null
  }

  get events (): Array<any> {
    const filtered = this.allEvents.filter(event =>
      this.activeFilters.includes(event.typeId)
    )
    const allNotes = this.allNotes.map(note => ({
      ...note,
      allDay: true
    }))
    const eventsWithNotes = [...filtered, ...allNotes]
    return eventsWithNotes
  }

  private openTooltip (info: any) {
    if (info.event.allDay) return // dont show for notes
    this.toolTip.isVisible = true
    this.toolTip.calendarEvent = info.event
    this.toolTip.element = info.el
  }

  private closeTooltip () {
    this.toolTip.isVisible = false
  }

  private handleAddNote (e: any): void {
    if (e.allDay) {
      // clicked on all day
      console.log('add note')
      this.addNoteDate = e.date
    } else {
      // clicked on blank date
    }
  }

  editEventHtml (info: any) {
    const id = info.event.extendedProps.typeId
    if (info.event.allDay) return // its a note
    const className = this.eventTypes.find(elem => elem.id === id).className
    info.el.classList.add('calendarEvent')
    info.el.classList.add(className)
  }

  private async handleChangeDates (e: any): Promise<void> {
    if (!this.allowUpdate) return // cancel if no permissions

    const eventCard: any = e.event
    if (eventCard.allDay) return e.revert()

    const dateFrom = dateToW3C(e.event.start)
    const dateTo = dateToW3C(e.event.end)
    const eventId: number = eventCard.id
    const params: any = new URLSearchParams()
    console.log(dateTo)

    params.append('id', eventId)
    params.append('date_at', dateFrom)
    params.append('date_end_at', dateTo)
    axios.post(this.URLUpdate, params)
  }

  private loadEvents (): void {
    const fullcalendar: any = this.$refs.fullCalendar
    const calApi: any = fullcalendar.getApi()

    const startDate: Date = calApi.view.activeStart // counting from 0 -> january
    const endDate: Date = calApi.view.activeEnd

    this.$emit('loadMonth', startDate)
    if (!isSameMonth(startDate, endDate)) {
      this.$emit('loadMonth', endDate)
    }
  }

  private inspectEvent (event: any): void {
    if (!this.eventClick.timeoutId) {
      this.eventClick.timeoutId = setTimeout(() => {
        // simple click
        clearTimeout(this.eventClick.timeoutId)
        this.eventClick.timeoutId = null
        this.eventClick.eventClicked = event.el
      }, 250) // tolerance in ms
    } else {
      // double click
      clearTimeout(this.eventClick.timeoutId)
      this.eventClick.timeoutId = null
      this.eventClick.eventClicked = event.el
      const linkToInspect = `${this.URLInspectEvent}?id=${event.event.id}`
      window.open(linkToInspect)
    }
  }
}
</script>

<style lang='less'>
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
@import "~@fullcalendar/timegrid/main.css";
.calendarEvent {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  cursor: pointer;
  &.blue {
    background-color: blue;
  }
  &.green {
    background-color: green;
  }
  &.red {
    background-color: red;
  }
  &.yellow {
    background-color: yellow;
    color: black;
  }
  // override calendar themes

  .fc-time {
    // margin-top: 10px;
    // font-size: 15px;
  }
  .fc-title {
    // margin-top: 10px;
    // text-shadow: 2px 2px #5e5e5e;
    // font-size: 22px;
  }
}
.fc-content-skeleton {
  td {
    cursor: copy;
  }
}
</style>
