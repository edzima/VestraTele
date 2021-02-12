export interface Filter {
    isActive: boolean,
    id: number,
    label: string,
    itemOptions?: {
        color?: string
    };
}
