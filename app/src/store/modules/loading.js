
const state = {
  keys: []
}

const getters = {
  has: state => (...args) => state.keys.indexOf(args.join('/')) > -1
}

const actions = {
  start ({ commit, getters }, key) {
    if (!getters.has(key)) {
      commit('add', key)
    }
  },
  end ({ commit, getters }, key) {
    if (getters.has(key)) {
      commit('remove', key)
    }
  }
}

const mutations = {
  add (state, key) {
    state.keys.push(key)
  },
  remove (state, key) {
    state.keys.splice(state.keys.indexOf(key), 1)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
