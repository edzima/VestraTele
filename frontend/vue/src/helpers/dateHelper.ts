const moment = require('moment');

export const dateToW3C = (date: Date): string => {
    if (!date) return '';
    return moment(date).format('YYYY-MM-DD HH:mm:ss');
};

export const getLastOfMonth = (date: Date): Date => {
    return moment(date).endOf('month').toDate();
};
export const getFirstOfMonth = (date: Date): Date => {
    return moment(date).startOf('month').toDate();
};
export const isSameMonth = (firstDate: Date, secondDate: Date): boolean => {
    return moment(firstDate).month() === moment(secondDate).month();
};
export const prettify = (date: string): string => {
    moment.locale('pl');
    return moment(date).format('Do MMMM YYYY');
};
export const prettifyHourRange = (dateStart: string, dateEnd: string): string => {
    const startDate = moment(dateStart).format('HH:mm');
    const endDate = moment(dateEnd).format('HH:mm');
    return dateEnd ? startDate + ' - ' + endDate : startDate
};
export const isSameDate = (firstDate: Date, secondDate: Date): boolean => {
    const first = moment(firstDate).format('LL');
    const second = moment(secondDate).format('LL');
    return first === second;
};
