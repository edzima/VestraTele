<template>
    <div class="filter-group">
        <h3 v-if="filterGroup.title">{{filterGroup.title}}</h3>
        <ul class="filters-nav nav nav-pills">
            <li v-for="filter in filterGroup.filters"
                :class="{active:filter.isActive}"
                class="filter-item nav-item"
            >
                <a @click="filterClick(filter)"
                    class="btn nav-link filter-btn"
                    :style="itemStyle(filter)">
                    {{ filter.label }}
                </a>

            </li>
        </ul>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {Filter, FilterGroup} from "@/types/Filter";

@Component({})
export default class Filters extends Vue {
    @Prop() filterGroup!: FilterGroup;

    itemStyle(filter: Filter): any {
        return {
            backgroundColor : filter.color
        }
    }

    filterClick(filter: Filter) {
        const newFilters = this.newGroupState(filter);
        this.$emit('groupUpdate', newFilters);
    }

    newGroupState(filterToToggle: Filter){
        const updatedFilterGroup = this.filterGroup;

        updatedFilterGroup.filters =  this.filterGroup.filters.map((filter: Filter)=>{
            let clone = filter
            if(filter.value === filterToToggle.value){
                clone.isActive = !filterToToggle.isActive
            }
            return clone
        })
        return updatedFilterGroup;
    }

}
</script>

<style scoped lang="less">
.filter-group {
    margin: 0 auto;
    text-align: center;

    .filters-nav {

        .filter-item {

            .filter-btn {
                color: white;
                box-shadow: 0 0 3px rgba(93, 87, 87,0.3);

                &:hover {
                    box-shadow: 0 0 5px rgba(93, 87, 87,0.7);
                    color: white;
                }
            }

            &:not(.active) {
                opacity: 0.5;
            }
        }

    }
}
</style>
