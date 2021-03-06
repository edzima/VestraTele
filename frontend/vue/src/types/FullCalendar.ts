export interface EventObject {
    id: number,
    groupId: string,
    allDay: boolean,
    start: Date,
    end?: string,
    title: string,
    url?: string
    color?: string,
    extendedProps: EventProps | any;
    source?: EventSourceObject
    setProp: Function;
    remove: Function;
}

export interface EventProps {
    tooltipContent?: string
}

export interface EventSourceObject {
    id?: number,
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

export interface DateClickInfo {
    date: Date;
    dateStr: string;
    allDay: boolean;
    dayEl: HTMLElement;
    jsEvent: MouseEvent;
    view: ViewObject;
}

export interface DateClickWithDayEvents extends DateClickInfo {
    dayEvents: EventObject[];
}

