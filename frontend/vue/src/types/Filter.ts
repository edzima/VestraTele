export interface Filter {
    isActive: boolean,
    id: number,
    label: string,
    itemOptions?: {
        color?: string
    };
}


export interface FiltersCollection {

    getFilter(id: number): Filter | undefined;

    getFilters(): Filter[];

    getActiveFilters(): Filter[];

    getActiveFiltersIds(): number[]
}
