const moment = require('moment')

export const ISOtoW3C = (date: string): string => {
  return moment(date).format('Y-m-dH:i:sP')
}
