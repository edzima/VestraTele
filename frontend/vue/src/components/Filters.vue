<template>
	<ul class="filters-nav nav nav-pills">
		<li v-for="filter in renderFilters"
				:class="{active:filter.isActive}"
				class="filter-item nav-item"
		>
			<a @click="onClick(filter)" href="#" class="btn nav-link filter-btn" :style="itemStyle(filter)">{{filter.label}}</a>
		</li>
	</ul>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import {Filter, FiltersCollection} from "@/types/Filter";

    @Component({})
    export default class Filters extends Vue implements FiltersCollection {
        @Prop() filters!: Filter[];


        get renderFilters(): Filter[] {
            return this.filters;
        }

        itemStyle(filter: Filter): any {
            if (!filter.itemOptions) {
                return;
            }
            let style: any = {};
            style.backgroundColor = filter.itemOptions.color;
            return style;
        }


        private onClick(filter: Filter): void {
            this.toggleFilter(filter);
            this.$emit('toggleFilter', filter, this.getActiveFiltersIds());
        }

        private toggleFilter(filter: Filter): void {
            this.filters.map((el) => {
                if (el.id === filter.id) {
                    el.isActive = !el.isActive;
                }
            });
        }

        getFilters(): Filter[] {
            return this.filters;
        }

        getFilter(id: number): Filter | undefined {
            return this.filters.find((filter) => filter.id === id);
        }

        getActiveFiltersIds(): number[] {
            return this.getActiveFilters().map((filter) => filter.id);
        }

        getActiveFilters(): Filter[] {
            return this.filters.filter((filter) => filter.isActive);
        }

    }
</script>

<style scoped lang="less">
	.filters-nav {
		.filter-item {
			opacity: 0.8;

			&:hover {
				opacity: 1;
			}

			.filter-btn {
				color: white;

				&:hover {
					color: white;
				}

			}

			&:not(.active) {
				opacity: 0.5;
			}
		}
	}
</style>
