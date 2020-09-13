import Vue from 'vue'
import Vuex from 'vuex'
import createLogger from 'vuex/dist/logger'

import loading from './modules/loading'
import site from './modules/site'
import LessonsService from '@/services/LessonsService'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  state: {
    errors: [],
    lessons: {},
    lessonsIdsByDate: {},
    lessonsIdsByMonth: {},
    lessonsIdsByChapter: {}
  },
  mutations: {
    addError (state, error) {
      error.time = new Date().getTime()
      state.errors.unshift(error)
    },
    setLessons (state, items) {
      items.forEach(item => {
        Vue.set(state.lessons, item.id, item)
      })
    },
    setLessonsIdsByDate (state, items) {
      items.forEach(item => {
        if (state.lessonsIdsByDate[item.date] == null) {
          Vue.set(state.lessonsIdsByDate, item.date, [])
        }
        if (state.lessonsIdsByDate[item.date].indexOf(item.id) === -1) {
          state.lessonsIdsByDate[item.date].push(item.id)
        }
      })
    },
    setLessonsIdsByMonth (state, items) {
      items.forEach(item => {
        const month = item.date.split('-').slice(0, 2).join('-')
        if (state.lessonsIdsByMonth[month] == null) {
          Vue.set(state.lessonsIdsByMonth, month, [])
        }
        if (state.lessonsIdsByMonth[month].indexOf(item.id) === -1) {
          state.lessonsIdsByMonth[month].push(item.id)
        }
      })
    },
    setLessonsIdsByChapter (state, { chapter, items }) {
      items.forEach(item => {
        if (state.lessonsIdsByChapter[chapter] == null) {
          Vue.set(state.lessonsIdsByChapter, chapter, [])
        }
        if (state.lessonsIdsByChapter[chapter].indexOf(item.id) === -1) {
          state.lessonsIdsByChapter[chapter].push(item.id)
        }
      })
    },
    setLessonsAsRead (state, ids) {
      ids.forEach(id => {
        if (state.lessons[id]) {
          state.lessons[id].is_read = true
          state.lessons[id].user_reads_count++
        }
      })
    }
  },
  getters: {
    getLesson: state => id => state.lessons[id] || null,
    getLessonsIdsByDate: state => date => state.lessonsIdsByDate[date] || [],
    getLessonsIdsByMonth: state => date => state.lessonsIdsByMonth[date] || [],
    getLessonsIdsByChapter: state => chapter => state.lessonsIdsByChapter[chapter] || [],

    getLessonsByDate: (state, getters) => date => getters.getLessonsIdsByDate(date).map(id => getters.getLesson(id)),
    getLessonsByMonth: (state, getters) => date => getters.getLessonsIdsByMonth(date).map(id => getters.getLesson(id)),
    getLessonsByChapter: (state, getters) => chapter => getters.getLessonsIdsByChapter(chapter).map(id => getters.getLesson(id)),

    isLoadingLessonsByDate: (_1, _2, _3, rootGetters) => date => rootGetters['loading/has']('loadLessonsByDate', date),
    isLoadingLessonsByMonth: (_1, _2, _3, rootGetters) => date => rootGetters['loading/has']('loadLessonsByMonth', date),
    isLoadingLessonsByChapter: (_1, _2, _3, rootGetters) => chapter => rootGetters['loading/has']('isLoadingLessonsByChapter', chapter),
    isMarkingLessonAsRead: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('markLessonAsRead', id)
  },
  actions: {
    bootstrap ({ dispatch }, data) {
      dispatch('site/setIdentity', data.user)
    },
    login () {
      // TODO: add something here
    },
    async loadLessonsByDate ({ dispatch, commit, getters }, date) {
      if (getters.isLoadingLessonsByDate(date)) {
        return null
      }
      dispatch('loading/start', `loadLessonsByDate/${date}`, { root: true })
      const { data } = await LessonsService.byDate(date)
      commit('setLessons', data)
      commit('setLessonsIdsByDate', data)
      dispatch('loading/end', `loadLessonsByDate/${date}`, { root: true })
    },
    async loadLessonsByMonth ({ dispatch, commit, getters }, date) {
      if (getters.isLoadingLessonsByMonth(date)) {
        return null
      }
      dispatch('loading/start', `loadLessonsByMonth/${date}`, { root: true })
      const { data } = await LessonsService.byMonth(date + '-01')
      commit('setLessons', data)
      commit('setLessonsIdsByDate', data)
      commit('setLessonsIdsByMonth', data)
      dispatch('loading/end', `loadLessonsByMonth/${date}`, { root: true })
    },
    async loadLessonsByChapter ({ dispatch, commit, getters }, chapter) {
      if (getters.isLoadingLessonsByChapter(chapter)) {
        return null
      }
      dispatch('loading/start', `loadLessonsByChapter/${chapter}`, { root: true })
      const { data } = await LessonsService.byChapter(chapter)
      commit('setLessons', data)
      commit('setLessonsIdsByChapter', { chapter, items: data })
      dispatch('loading/end', `loadLessonsByMonth/${chapter}`, { root: true })
    },
    async markLessonAsRead ({ dispatch, commit, getters }, id) {
      if (getters.isMarkingLessonAsRead(id)) return null
      dispatch('loading/start', `markLessonAsRead/${id}`, { root: true })
      try {
        await LessonsService.markAsRead(id)
        commit('setLessonsAsRead', [id])
      } catch (err) {}
      dispatch('loading/end', `markLessonAsRead/${id}`, { root: true })
    },
    addError ({ commit }, error) {
      commit('addError', error)
    }
  },
  modules: {
    loading,
    site
  },
  strict: debug,
  plugins: debug ? [createLogger()] : []
})
