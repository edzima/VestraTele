<template>
    <div>
        <FilterManager
            ref="filterManager"
            :filters="filters"
            @toggleFilter="toggleFilter"
        />
        <Calendar
            ref="calendar"
            :eventSources="eventSources"
            :eventRender="eventRender"
            :editable="editable"
            @dateClick="emitDateClick"
            @dateDoubleClick="emitDateDoubleClick"
            @eventEdit="emitEventEdit"
        />
    </div>
</template>

<script lang="ts">
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import FilterManager from "@/components/Filters.vue";
import Calendar from "@/components/Calendar.vue";
import {Filter} from "@/types/Filter";
import {DateClickWithDayEvents, EventInfo, EventSourceObject} from "@/types/FullCalendar";
import {telLink} from "@/helpers/HTMLHelper";

@Component({
    components: {Calendar, FilterManager}
})
export default class FilterCalendar extends Vue {
    @Prop() filters!: Filter[];
    @Prop() private readonly eventSources!: EventSourceObject[];
    @Prop({
        default: () => true,
        type: Boolean,
    })
    private editable!: boolean; // allow user to edit events

    @Ref() calendar!: Calendar;
    @Ref() filterManager!: FilterManager;

    private visibleStatusIds: any[] = [];

    mounted(): void {
        this.visibleStatusIds = this.filterManager.getActiveFiltersIds();
    }

    private toggleFilter(ids: number[]): void {
        this.visibleStatusIds = ids;
        this.calendar.rerenderEvents();
    }

    private eventRender(info: EventInfo): void {
        this.parseVisible(info);
        this.parsePhone(info);
    }

    private parseVisible(info: EventInfo): void {
        const status = info.event.extendedProps.statusId;
        if (status) {
            if (this.visibleStatusIds.includes(status)) {
                info.el.classList.remove('hide');
            } else {
                info.el.classList.add('hide')
            }
        }
    }

    private parsePhone(info: EventInfo): void {
        const phone = info.event.extendedProps.phone;
        if (phone) {
            const title = info.el.querySelector('.fc-title');
            if (title) {
                title.innerHTML = telLink(phone, info.event.title).outerHTML;
            }
        }
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

    public filterEvents(events: EventInfo[]): EventInfo[]{
        return events.map((event) => {
            const filter = this.filterManager.getFilter(event.statusId);
            if (filter && filter.itemOptions) {
                event = Object.assign(event, filter.itemOptions);
            }
            return event;
        })
    }

}
</script>
