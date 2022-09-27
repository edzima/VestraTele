export interface EventObject {
    id: number,
    groupId: string,
    allDay: boolean,
    start: Date,
    end: Date,
    title: string,
    url?: string
    color?: string,
    extendedProps: EventProps | any;
    source?: EventSourceObject
    setProp: Function;
    remove: Function;
}

export interface EventProps {
    isNote?: boolean,
    tooltipContent?: string
}

export interface EventSourceObject {
    id?: number,
    url?: string,
    allDayDefault?: boolean
    extraParams?: any,
    editable?: boolean
    success?: Function,
}

export interface EventSourceConfig extends EventSourceObject {
    urlUpdate: string,
}


export interface EventInfo {
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

