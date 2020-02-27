const CalendarCore = require('@fullcalendar/core')
const moment = require('moment')

export const dateToW3C = (date: Date): string => {
  if (!date) return ''
  const parseSettings = {
    month: '2-digit',
    year: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
    meridiem: false,
    separator: '-'
  }
  const dateISO: string = CalendarCore.formatDate(
    date.toISOString(), parseSettings
  )
  const splitted = dateISO.split('/')
  const month = splitted[0]
  const day = splitted[1]
  const year = splitted[2].split(',')[0]
  const time = splitted[2].split(',')[1]
  // "2020-02-28 12:27:00"
  const dateFormatted = `${year}-${month}-${day} ${time}`
  return dateFormatted
}

export const getLastOfMonth = (date: Date): string => {
  const endDate = moment(date).endOf('month')
  return dateToW3C(endDate)
}
export const getFirstOfMonth = (date: Date): string => {
  const startDate = moment(date).startOf('month')
  return dateToW3C(startDate)
}
export const isSameMonth = (firstDate: Date, secondDate: Date): boolean => {
  return moment(firstDate).month() === moment(secondDate).month()
}
