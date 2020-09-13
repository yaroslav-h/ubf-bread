import moment from 'moment'

export const yyyyMmDd = (inp) => {
  return moment(inp).format('YYYY-MM-DD')
}
