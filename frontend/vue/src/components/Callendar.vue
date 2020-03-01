<template>
  <div class="calendar">
    <NotesPopup
      :date="noteOpenedDate"
      :allNotes="allNotes"
      @close="closePopup"
      @deleteNote="deleteNote"
      @addNote="addNote"
      @editNoteText="editNoteText"
    />
    <ToolTip
      :calendarEvent="toolTip.calendarEvent"
      :element="toolTip.element"
      :isVisible="toolTip.isVisible"
      :activeView="toolTip.activeView"
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
      :eventRender="editEventHtml"
      :height="calendar.height"
      @eventDrop="handleChangeDates"
      @eventResize="handleChangeDates"
      @eventClick="inspectEvent"
      @dateClick="handleDateClick"
      @eventMouseEnter="openTooltip"
      @eventMouseLeave="closeTooltip"
      @eventDragStart="closeTooltip"
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
import { isSameMonth } from '@/helpers/dateHelper.ts'
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
    required: false
  })
  private eventTypes!: Array<any>;

  @Prop({ required: true })
  private allEvents!: Array<any>;

  @Prop({ required: true })
  private activeFilters!: Array<number>;

  @Prop({})
  private allNotes!: Array<any>;

  private calendar: any = {
    plugins: [dayGridPlugin, listWeekPlugin, timeGridPlugin, interactionPlugin],
    header: {
      left: 'today prev,next',
      center: 'title',
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
    },
    height: 'auto'
  };

  private deleteNote (noteID: number) {
    // handle delete
    this.$emit('deleteNote', noteID)
  }

  private editNoteText (noteID: number, text: string) {
    this.$emit('editNoteText', noteID, text)
  }

  private eventClick: any = {
    eventClicked: null,
    timeoutId: null
  };

  private dateClick: any = {
    date: null,
    timeoutId: null
  };

  private noteOpenedDate: any = null;

  private toolTip: any = {
    isVisible: false,
    element: null,
    calendarEvent: null,
    activeView: null
  };

  private closePopup () {
    this.noteOpenedDate = null
  }

  get events (): Array<any> {
    // filter events and add notes to render on calendar
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
    this.toolTip = {
      isVisible: true,
      calendarEvent: info.event,
      element: info.el,
      activeView: info.view.type
    }
  }

  private closeTooltip () {
    this.toolTip.isVisible = false
  }

  private openNotes (e: any): void {
    this.noteOpenedDate = e.date ? e.date : e.start
  }

  private handleDateClick (e) {
    // if its a month view allow only to add events, not notes
    if (e.view && e.view.type !== 'dayGridMonth') {
      if (e.allDay) {
        return this.openNotes(e)
      }
    }
    if (!this.dateClick.timeoutId) {
      this.dateClick.timeoutId = setTimeout(() => {
        // simple click
        clearTimeout(this.dateClick.timeoutId)
        this.dateClick.timeoutId = null
        this.dateClick.date = e.date
      }, 200) // tolerance in ms
    } else {
      // double click
      clearTimeout(this.dateClick.timeoutId)
      this.dateClick.timeoutId = null
      this.dateClick.date = e.date
      this.$emit('dateClick', this.dateClick.date)
    }
  }

  private addNote (noteText: string, day: Date) {
    this.$emit('addNote', noteText, day)
  }

  editEventHtml (info: any) {
    const id = info.event.extendedProps.typeId
    info.el.classList.add('calendarEvent')

    if (info.event.allDay) {
      info.el.classList.add('note')
      return
    } // its a note

    if (!id) return // its a note beeing dragged
    const className = this.eventTypes.find(elem => elem.id === id).className
    info.el.classList.add(className)
  }

  private handleChangeDates (e: any): void {
    if (!this.allowUpdate) return // cancel if no permissions
    this.$emit('eventEdit', e)
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
    if (event.event.allDay) {
      this.openNotes(event.event)
    }
    if (!this.eventClick.timeoutId) {
      this.eventClick.timeoutId = setTimeout(() => {
        // simple click
        clearTimeout(this.eventClick.timeoutId)
        this.eventClick.timeoutId = null
        this.eventClick.eventClicked = event.el
      }, 200) // tolerance in ms
    } else {
      // double click

      clearTimeout(this.eventClick.timeoutId)
      this.eventClick.timeoutId = null
      this.eventClick.eventClicked = event.el
      this.$emit('eventDoubleClick', event.event.id)
    }
  }

  // private disableToolTipOnScroll () {
  //   const calendar = document.getElementsByClassName('fc-time-grid-container')[0]
  //   calendar.addEventListener('scroll', this.closeTooltip)
  // }

  mounted () {
    // this.disableToolTipOnScroll()
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
  &.note{
    display: block;
    background-color: rgb(97, 0, 136);
    .fc-title {
    white-space: nowrap;
    }
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
    white-space: normal;
  }
}
.fc-content-skeleton {
  td {
    // cursor: copy;
  }
}
</style>
