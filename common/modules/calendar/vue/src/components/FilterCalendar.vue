<template>
    <div>
        <div class="filter-bar">
            <FilterManager
                v-for="filterGroup in usableFilterGroups"
                :key="filterGroup.filteredPropertyName"
                :filterGroup="filterGroup"
                @groupUpdate="refreshFilterGroups"
            />
        </div>
        <Calendar
            ref="calendar"
            :eventSources="eventSources"
            :eventRender="eventRender"
            :editable="editable"
            @dateClick="emitDateClick"
            @dateDoubleClick="emitDateDoubleClick"
            @eventEdit="emitEventEdit"
            @eventClick="eventClick"
        />
    </div>
</template>

<script lang="ts">
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import FilterManager from "@/components/FilterManager.vue";
import Calendar from "@/components/Calendar.vue";
import {Filter, FilterGroup} from "@/types/Filter";
import {DateClickWithDayEvents, EventInfo, EventObject, EventSourceObject} from "@/types/FullCalendar";
import {createBadge} from "@/helpers/HTMLHelper";

@Component({
    components: {Calendar, FilterManager}
})
export default class FilterCalendar extends Vue {
  @Prop() filterGroups!: FilterGroup[];
  @Ref() calendar!: Calendar;
  @Ref() filterManager!: FilterManager;
  usableFilterGroups: FilterGroup[] = [];
  @Prop() private readonly eventSources!: EventSourceObject[];
  @Prop({default: () => true}) private readonly notesEnabled!: boolean
  @Prop({default: () => true}) private editable!: boolean; // allow user to edit events

  mounted(): void {
    this.usableFilterGroups = this.filterGroups;
  }

  refreshFilterGroups(updatedGroup: FilterGroup): void {
    this.usableFilterGroups = this.usableFilterGroups.map((oldGroup: FilterGroup) => {
      if (oldGroup.id === updatedGroup.id) {
        return updatedGroup
      }
      return oldGroup
    })
    this.rerenderCalendar()
  }

  parseEventStyles(eventInfo: EventInfo): void {
    this.usableFilterGroups.forEach((filterGroup: FilterGroup) => {
      filterGroup.filters.forEach((filter: Filter) => {
        if (!filter.badge) return;
        const key = filterGroup.filteredPropertyName;
        if (eventInfo.event.extendedProps[key] === filter.value) {
          this.parseBadge(filter, eventInfo);
        }
      })
    })
  }

  public rerenderCalendar(): void {
    this.calendar.rerenderEvents();
  }

  public updateCalendarEventProp(event: EventObject, propName: string, newContent: string | number) {
    this.calendar.updateCalendarEventProp(event, propName, newContent)
  }

  public deleteEventById(id: number): void {
    return this.calendar.deleteEventById(id);
  }

  public findCalendarEvent(id: number): EventObject {
    return this.calendar.findCalendarEvent(id)
  }

  private eventRender(eventInfo: EventInfo): boolean {
    if (this.parseVisible(eventInfo)) {
      this.parsePhone(eventInfo);
      this.parseEventStyles(eventInfo);
      return true;
    }
    return false;
  }

  private parseVisible(eventInfo: EventInfo): boolean {
    if (this.eventShouldVisible(eventInfo.event)) {
      this.revealEvent(eventInfo);
      return true;
    }
    this.hideEvent(eventInfo);
    return false;
  }

  private eventShouldVisible(event: EventObject): boolean {
    let allGroupVisible = true;

    this.usableFilterGroups.forEach((filterGroup: FilterGroup) => {
      allGroupVisible = allGroupVisible && this.checkIsEventVisibleInGroup(event, filterGroup);
    })
    return allGroupVisible
  }

  private emitDateClick(data: DateClickWithDayEvents) {
    this.$emit('dateClick', data);
  }

  private emitDateDoubleClick(data: DateClickWithDayEvents) {
    this.$emit('dateDoubleClick', data);
  }

  private emitEventEdit(e: any) {
    this.$emit('eventEdit', e);
  }

  private checkIsEventVisibleInGroup(event: EventObject, filterGroup: FilterGroup): boolean {
    const key = filterGroup.filteredPropertyName;
    if (!(key in event.extendedProps)) {
      return true
    }
    const filteredEventValue = event.extendedProps[key];
    return filterGroup
        .filters
        .some(
            (filter: Filter) => filter.isActive && filter.value === filteredEventValue
        );
  }

  private hideEvent(eventInfo: EventInfo): void {
    eventInfo.event.setProp('display', 'none')
  }

  private revealEvent(eventInfo: EventInfo): void {
    eventInfo.event.setProp('display', 'auto')
  }

  private parsePhone(info: EventInfo): void {
    const phone = info.event.extendedProps.phone;
    if (phone) {
      const title = info.el.querySelector('.fc-title');
      if (title) {
        title.innerHTML += '<br>' + phone;
      }
    }
  }

  private eventClick(event: EventObject) {
    this.$emit('eventClick', event)
  }

  private parseBadge(filter: Filter, event: EventInfo): void {
    if (filter.badge) {
      const badgeColor = filter.badge.background;
      const badgeText = filter.badge.text;
      if (badgeColor && badgeText) {
        this.appendBadge(event, badgeColor, badgeText);
      }
    }
  }

  private appendBadge(event: EventInfo, badgeColor: string, text: string): void {
    event.el.appendChild(
        createBadge(badgeColor, text)
    );
  }
}
</script>
<style>
.filter-bar {
    display: flex;
    flex-direction: row;
    height: auto;
    width: 100%;
}
</style>
