<template>
  <div class="calendar">
    <Filters
    :eventTypes="eventTypes"
    :activeFilters="activeFilters"
    @toggleFilter="toggleFilter"
    />
    <Calendar
    :eventTypes="eventTypes"
    :activeFilters="activeFilters"
    :fetchedEvents="fetchedEvents"
    :allowUpdate="allowUpdate"
    :agentId="agentId"
    :URLUpdate="URLUpdate"
    :URLGetEvents="URLGetEvents"
    :URLInspectEvent="URLInspectEvent"
    />
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import Calendar from '@/components/Callendar.vue'
import Filters from '@/components/Filters.vue'
import axios from 'axios'
@Component({
  components: {
    Calendar,
    Filters
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
  private URLGetEvents!: string;

  @Prop({
    default: () => 'http://google.com',
    // -----PARAMS-----
    // dateFrom
    // dateTo
    // agentId
    required: false
  })
  private URLInspectEvent!: string;

  private activeFilters: Array<any> = [1, 2, 3, 4];
  private eventTypes: Array<any> = [{ id: 1, name: 'umówiony' }, { id: 2, name: 'umowa' }, { id: 3, name: 'niepodpisany' }, { id: 4, name: 'wysłane dokumenty' }]
  private fetchedEvents: Array<any> = []

  private toggleFilter (filterId: number): void {
    if (this.activeFilters.includes(filterId)) {
      const filtered = this.activeFilters.filter(id => id !== filterId)
      this.activeFilters = filtered
    } else {
      this.activeFilters.push(filterId)
    }
  }

  private async fetchEvents (): Promise<void> {
    const res = await axios.get(this.URLGetEvents, {
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

    this.fetchedEvents = fullCalendarEvents
  }

  created () {
    this.fetchEvents()
  }
}
</script>

<style scoped lang='less'>
</style>
