export interface Filter {
    isActive: boolean,
    label: string,
    color: string
    value: string | number
    eventColors?: {
        background?: string
        badge?: string
        border?: string
    };
}

export interface FilterGroup {
    title: string,
    id: number
    filteredPropertyName: string
    filters: Filter[]
}
