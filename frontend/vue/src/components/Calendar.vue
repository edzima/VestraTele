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
      :isAllContentVisible="isTitlesHidden"
    />
    <FullCalendar
      ref="fullCalendar"
      :defaultView="calendar.defaultView"
      :header="calendar.header"
      :plugins="calendar.plugins"
      :events="visibleEvents"
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
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import listWeekPlugin from '@fullcalendar/list';
    import plLang from '@fullcalendar/core/locales/pl';
    import interactionPlugin from '@fullcalendar/interaction';
    import timeGridPlugin from '@fullcalendar/timegrid';
    import {isSameMonth} from '@/helpers/dateHelper.ts';
    import NotesPopup from './NotesPopup.vue';
    import ToolTip from './ToolTip.vue';
    import {MeetingType} from '@/types/MeetingType.ts';
    import {CalendarEvent} from '@/types/CalendarEvent.ts';
    import {CalendarNote} from '@/types/CalendarNote.ts';
    import {arrayRemoveObjectsDuplicates} from "@/helpers/arrayHelper";

    const FullCalendar = require('@fullcalendar/vue').default;

    type toolTipType = {
        isVisible: boolean;
        calendarEvent: any; // wybacz szefie dodam tutaj typy z fullcalendara obiecuje
        element: any;
        activeView: any;
    }
    type eventClickType = {
    eventClicked: any;
    timeoutId: number;
  }
  type dateClickType = {
    date: Date;
    timeoutId: number;
  }

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
      default: () => false
    })
    private isTitlesHidden!: boolean;

    @Prop({
      required: true
    })
    private eventTypes!: MeetingType[];

  @Prop({ required: true })
  private allEvents!: CalendarEvent[];

  @Prop({ required: true })
  private activeTypes!: number[];

  @Prop({})
  private allNotes!: CalendarNote[];

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

  get visibleEvents (): Array<any> {
    return [...this.filterEvents(), ...this.allNotes];
  }

  private deleteNote (noteID: number): void {
    // handle delete
    this.$emit('deleteNote', noteID);
  }

    private eventClick: eventClickType = {
      eventClicked: null,
      timeoutId: 0
    };

  private dateClick: dateClickType = {
    date: new Date(),
    timeoutId: 0
  };

    private noteOpenedDate: any = null;

    private toolTip: toolTipType = {
      isVisible: false,
      element: null,
      calendarEvent: null,
      activeView: null
    };

    private editNoteText (noteID: number, text: string): void {
      this.$emit('editNoteText', noteID, text);
    }

    private closePopup (): void {
      this.noteOpenedDate = null;
    }

    private filterEvents (): CalendarEvent[] {
        const filtered = this.allEvents.filter(event =>
            this.activeTypes.includes(event.typeId)
        );
        return arrayRemoveObjectsDuplicates(filtered, 'id');
    }

    private openTooltip (info: any): void {
      if (info.event.allDay) return; // dont show for notes
      this.toolTip = {
        isVisible: true,
        calendarEvent: info.event,
        element: info.el,
        activeView: info.view.type
      };
    }

    private closeTooltip (): void {
      this.toolTip.isVisible = false;
    }

    private openNotes (e: any): void {
      this.noteOpenedDate = e.date ? e.date : e.start;
    }

    private handleDateClick (e): void {
    // if its a month view allow only to add events, not notes
      if (e.view && e.view.type !== 'dayGridMonth') {
        if (e.allDay) {
          return this.openNotes(e);
        }
      }
      if (!this.dateClick.timeoutId) {
        this.dateClick.timeoutId = setTimeout(() => {
        // simple click
          clearTimeout(this.dateClick.timeoutId);
          this.dateClick.timeoutId = 0;
          this.dateClick.date = e.date;
        }, 200); // tolerance in ms
      } else {
      // double click
        clearTimeout(this.dateClick.timeoutId);
        this.dateClick.timeoutId = 0;
        this.dateClick.date = e.date;
        this.$emit('dateClick', this.dateClick.date);
      }
    }

    private addNote (noteText: string, day: Date): void {
      this.$emit('addNote', noteText, day);
    }

    private editEventHtml (info: any): void {
      const id = info.event.extendedProps.typeId;
      info.el.classList.add('calendarEvent');
      if (this.isTitlesHidden) {
        info.el.classList.add('hideTitle');
      }

      if (info.event.allDay) {
        info.el.classList.add('note');
        return;
      } // its a note
      if (!id) return; // its a note beeing dragged
      const meetType = this.getType(id);

      if (meetType) {
        info.el.classList.add(meetType.className);
      }

        // add row for adress
      const newElem = document.createElement('p');
        newElem.innerHTML = info.event.extendedProps.city;
        newElem.className = 'fc-client';
      info.el.children[0].appendChild(newElem);
    }

    private getType (id: number): MeetingType {
      const model = this.eventTypes.find(elem => elem.id === id);
      if (!model) {
        throw Error('type error');
      }
      return model;
    }

    private handleChangeDates (e: any): void {
      if (!this.allowUpdate) return e.revert(); // cancel if no permissions
      if (e.oldEvent) { // cancel if note is dragged to event and vice-versa
        if (e.event.allDay !== e.oldEvent.allDay) return e.revert();
      }
      this.$emit('eventEdit', e);
    }

    private loadEvents (): void {
      const fullcalendar: any = this.$refs.fullCalendar;
      const calApi: any = fullcalendar.getApi();

      const startDate: Date = calApi.view.activeStart; // counting from 0 -> january
      const endDate: Date = calApi.view.activeEnd;

      this.$emit('loadMonth', startDate);
      if (!isSameMonth(startDate, endDate)) {
        this.$emit('loadMonth', endDate);
      }
    }

    private inspectEvent (event: any): void {
      if (event.event.allDay) {
        this.openNotes(event.event);
      }
      if (!this.eventClick.timeoutId) {
        this.eventClick.timeoutId = setTimeout(() => {
        // simple click
          clearTimeout(this.eventClick.timeoutId);
          this.eventClick.timeoutId = 0;
          this.eventClick.eventClicked = event.el;
        }, 200); // tolerance in ms
      } else {
      // double click

        clearTimeout(this.eventClick.timeoutId);
        this.eventClick.timeoutId = 0;
        this.eventClick.eventClicked = event.el;
        this.$emit('eventDoubleClick', event.event.id);
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
  &.note {
    display: block;
    background-color: rgb(97, 0, 136);
    .fc-title {
      white-space: nowrap;
    }
  }
  &.hideTitle{
    .fc-title{
      // display: none;
      opacity: 0;
    }
    .fc-client{
      // display: none;
      opacity: 0;
    }
    .fc-time{
      // display: none;
      opacity: 0;
    }
  }
}

.calendarEvent:not(.note) {
  .fc-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    .fc-time {
      // margin-right: auto;
      background-color: rgba(0, 0, 0, 0.281);
      width: 100%;
      font-weight: bold;
      span{
        margin-left: 2%;
      }
    }
    .fc-title {
      height: auto;
      text-align: center;
    }
    .fc-client{
      margin: 0;
      font-style: italic;
    }
  }
}
@media screen and (max-width: 600px) {
  .fc-toolbar.fc-header-toolbar{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
}
</style>
