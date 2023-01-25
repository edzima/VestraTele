export interface Filter {
    isActive: boolean,
    label: string,
    color: string
    value: string | number
    badge?: {
        text?: string// @todo rename to badgeBackground
        background?: string
        color?: string
    };
}

export interface FilterGroup {
    title: string,
    id: number
    filteredPropertyName: string
    filters: Filter[]
}
