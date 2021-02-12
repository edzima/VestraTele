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
        />
    </div>
</template>

<script lang="ts">
import {Component, Prop, Ref, Vue} from 'vue-property-decorator';
import FilterManager from "@/components/FilterManager.vue";
import Calendar from "@/components/Calendar.vue";
import {Filter, FilterGroup} from "@/types/Filter";
import {DateClickWithDayEvents, EventInfo, EventObject, EventSourceObject} from "@/types/FullCalendar";
import {createBadge, telLink} from "@/helpers/HTMLHelper";

@Component({
    components: {Calendar, FilterManager}
})
export default class FilterCalendar extends Vue {
    @Prop() filterGroups!: FilterGroup[];
    @Prop() private readonly eventSources!: EventSourceObject[];
    @Prop({
        default: () => true,
        type: Boolean,
    })
    private editable!: boolean; // allow user to edit events

    @Ref() calendar!: Calendar;
    @Ref() filterManager!: FilterManager;

    usableFilterGroups: FilterGroup[] = [];

    mounted(): void {
        this.usableFilterGroups = this.filterGroups;
    }

    checkGroupFilters(event: EventObject): boolean {
        let allGroupVisible = true;

        this.usableFilterGroups.forEach((filterGroup: FilterGroup) => {
            allGroupVisible = allGroupVisible && this.checkIsEventVisibleInGroup(event, filterGroup);
        })

        return allGroupVisible
    }

    checkIsEventVisibleInGroup(event: EventObject, filterGroup: FilterGroup): boolean {
        let isElementVisibleInGroup = false;
        const activeInGroup = this.activeValuesInFilterGroup(filterGroup);
        activeInGroup.forEach(value => {
            if (value === event.extendedProps[filterGroup.filteredPropertyName]) {
                isElementVisibleInGroup = true
            }
        })
        return isElementVisibleInGroup;
    }

    activeValuesInFilterGroup(filterGroup: FilterGroup): Filter["value"][] {
        const actives = filterGroup.filters.filter((filter: Filter) => filter.isActive);
        return actives.map((filter: Filter) => filter.value);
    }

    private eventRender(info: EventInfo): void {
        this.parseVisible(info);
        this.parsePhone(info);
        this.parseEventStyles(info);
    }

    refreshFilterGroups(updatedGroup: FilterGroup):void{
        this.usableFilterGroups = this.usableFilterGroups.map((oldGroup: FilterGroup)=>{
            if(oldGroup.id === updatedGroup.id){
                return updatedGroup
            }
            return oldGroup
        })
        this.calendar.rerenderEvents();

    }

    private parseVisible(eventInfo: EventInfo): void {
        try {
            const isVisible = this.checkGroupFilters(eventInfo.event);
            if (isVisible) {
                eventInfo.el.classList.remove('hide');
            } else {
                eventInfo.el.classList.add('hide')
            }
        } catch (err) {
            console.error(err);
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

    parseEventStyles(eventInfo:EventInfo):void{
        this.usableFilterGroups.forEach((filterGroup: FilterGroup)=>{
            filterGroup.filters.forEach((filter: Filter)=>{
                if(!filter.eventColors) return;
                const key = filterGroup.filteredPropertyName;
                if(eventInfo.event.extendedProps[key] === filter.value){
                    const backgroundColor = filter.eventColors.background

                    if(backgroundColor){
                        eventInfo.el.style.backgroundColor = backgroundColor;
                    }

                    const badgeColor = filter.eventColors.badge
                    if(badgeColor){
                        this.parseBadge(eventInfo.el, badgeColor);
                    }
                }
            })
        })
    }

    parseBadge(event: EventInfo["el"], badgeColor): void{
        console.log(event);
        const badgeElem = createBadge(badgeColor);
        const body = event.querySelector('.fc-content');
        body.appendChild(badgeElem);
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
