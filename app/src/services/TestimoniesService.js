import axios from 'axios'

import handleApiError from '@/helpers/handleApiError'

const BASE_URL = '/_/testimonies'

export default {
  async list () {
    return await axios.get(`${BASE_URL}/index`).catch(handleApiError)
  },
  async listByLesson (id) {
    return await axios.get(`${BASE_URL}/index`, { params: { lesson: id } }).catch(handleApiError)
  },
  async listByUser (id) {
    return await axios.get(`${BASE_URL}/index`, { params: { user: id } }).catch(handleApiError)
  },

  async view (id) {
    return await axios.get(`${BASE_URL}/view`, { params: { id, expand: 'lesson' } }).catch(handleApiError)
  },
  async viewByLesson (lesson) {
    return await axios.get(`${BASE_URL}/view`, { params: { lesson } }).catch(handleApiError)
  },

  async create (data) {
    return await axios.post(`${BASE_URL}/create`, data).catch(handleApiError)
  },
  async edit (id, data) {
    return await axios.post(`${BASE_URL}/update`, data, { params: { id } }).catch(handleApiError)
  },
  async remove (id) {
    return await axios.post(`${BASE_URL}/delete`, {}, { params: { id } }).catch(handleApiError)
  }
}
