import SiteService from '@/services/SiteService'

import Vue from 'vue'

const state = {
  identity: null,
  badges: {}
}

const getters = {
  isLoggedIn: state => state.identity != null,
  identity: state => state.identity,
  viewerId: state => state.identity ? state.identity.id : false,
  isViewer: state => id => state.identity ? state.identity.id === id : false,

  getBadgeCounter: state => name => state.badges[name] || { count: null, at: null }
}

const actions = {

  setIdentity ({ commit }, user) {
    if (user == null) {
      commit('unsetIdentity')
    } else {
      commit('setIdentity', user)
    }
  },

  async login ({ dispatch, commit }, form) {
    const { data, err } = await SiteService.login(form)

    if (err) throw err

    if (data) {
      dispatch('setIdentity', data.user)
      dispatch('login', data.user, { root: true })

      if (data.badges) {
        Object.keys(data.badges).forEach(name => {
          commit('site/setBadgeCounter', { name, ...data.badges[name] }, { root: true })
        })
      }
    }
  },

  async signup ({ dispatch }, form) {
    const { data, err } = await SiteService.signup(form)

    if (err) throw err

    if (data) {
      dispatch('setIdentity', data.viewer)
      dispatch('login', data.viewer, { root: true })
    }
  },

  async logout ({ dispatch }) {
    await SiteService.logout()

    dispatch('setIdentity')
    dispatch('logout', null, { root: true })
  }
}

const mutations = {
  setIdentity (state, user) {
    Vue.set(state, 'identity', user)
  },
  setBadgeCounter (state, { name, count, at }) {
    Vue.set(state.badges, name, {
      count, at
    })
  },
  unsetIdentity (state) {
    state.identity = null
  },
  updateIdentity (state, fields) {
    Object.keys(fields).forEach(field => {
      state.identity[field] = fields[field]
    })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
