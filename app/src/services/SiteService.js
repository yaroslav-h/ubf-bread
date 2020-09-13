import axios from 'axios'

import handleApiError from '@/helpers/handleApiError'

const BASE_URL = '/_/site'

export default {

  async login (data) {
    return await axios.post(`${BASE_URL}/login`, data).then(res => {
      return res
    }).catch(handleApiError)
  },

  async signup (data) {
    return await axios.post(`${BASE_URL}/signup`, data).then(res => {
      return res
    }).catch(handleApiError)
  },

  async subscribe (data) {
    return await axios.post(`${BASE_URL}/subscribe`, {
      type: 'web_push',
      info: data
    }).catch(handleApiError)
  },
  async unsubscribe (data) {
    return await axios.post(`${BASE_URL}/unsubscribe`, {
      type: 'web_push',
      info: data
    }).catch(handleApiError)
  },

  async recoverPassword (data) {
    return axios.post(`${BASE_URL}/recover-password`, data).catch(handleApiError)
  },

  logout () {
    return axios.post(`${BASE_URL}/logout`)
      .then(response => response.data)
  }
}
