import axios from 'axios'

import handleApiError from '@/helpers/handleApiError'

const BASE_URL = '/_/lessons'

export default {
  async today () {
    return await axios.get(`${BASE_URL}/today`).catch(handleApiError)
  },
  async byDate (date) {
    return await axios.get(`${BASE_URL}/date`, { params: { date } }).catch(handleApiError)
  },
  async byMonth (date) {
    return await axios.get(`${BASE_URL}/month`, { params: { date } }).catch(handleApiError)
  },
  async byChapter (name) {
    return await axios.get(`${BASE_URL}/chapter`, { params: { name } }).catch(handleApiError)
  },
  async getYears () {
    return await axios.get(`${BASE_URL}/get-years`).catch(handleApiError)
  },
  async getChapters (year) {
    return await axios.get(`${BASE_URL}/get-chapters`, { params: { year } }).catch(handleApiError)
  },
  async view (id) {
    return await axios.get(`${BASE_URL}/view`, { params: { id } }).catch(handleApiError)
  },
  async markAsRead (id) {
    return await axios.post(`${BASE_URL}/mark-as-read`, null, { params: { id } })
  }
}
