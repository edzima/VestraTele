export interface EventObject {
    id: number,
    groupId: string,
    allDay: boolean,
    start?: string,
    end?: string,
    title: string,
    url?: string
    color?: string,
    extendedProps: EventProps | any;
    source?: EventSourceObject
}

export interface EventProps {
    tooltipContent?: string
}

export interface EventSourceObject {
    url?: string,
    allDayDefault?: boolean
    extraParams?: any,
    success?: Function,
}


export interface Info {
    event: EventObject,
    el: HTMLElement,
    isMirror: boolean,
    isStart: boolean,
    isEnd: boolean,
    view: ViewObject,
}

export interface ViewObject {
    type: string,
    title: string,
    activeStart: Date,
    activeEnd: Date,
    currentStart: Date,
    currentEnd: Date
}
