export interface Filter {
    isActive: boolean,
    label: string,
    color: string
    value: string | number
    eventColors?: {
        background?: string
        badge?: string
    };
}

export interface FilterGroup {
    title: string,
    id: number
    filteredPropertyName: string
    filters: Filter[]
}
