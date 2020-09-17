import Vue from 'vue'
import Vuex from 'vuex'
import createLogger from 'vuex/dist/logger'

import loading from './modules/loading'
import site from './modules/site'
import LessonsService from '@/services/LessonsService'
import SiteService from '@/services/SiteService'
import TestimoniesService from '@/services/TestimoniesService'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  state: {
    errors: [],
    UI: {
      showLangSelector: false,
      locales: [],
      locale: null,
      isDarkMode: localStorage.getItem('isDarkMode') === '1'
    },
    lessons: {},
    lessonsIdsByDate: {},
    lessonsIdsByMonth: {},
    lessonsIdsByChapter: {},
    testimonies: {},
    testimonyByLesson: {},
    testimoniesIdsByKey: {}
  },
  mutations: {
    resetUserState (state) {
      state.lessons = {}
      state.lessonsIdsByDate = {}
      state.lessonsIdsByMonth = {}
      state.lessonsIdsByChapter = {}
      state.testimonies = {}
      state.testimonyByLesson = {}
      state.testimoniesIdsByKey = {}
    },
    addError (state, error) {
      error.time = new Date().getTime()
      state.errors.unshift(error)
    },
    setUIProp (state, { prop, value }) {
      state.UI[prop] = value
      if (prop === 'isDarkMode') {
        localStorage.setItem('isDarkMode', (value ? '1' : '0'))
      }
    },
    setLessons (state, items) {
      items.forEach(item => {
        Vue.set(state.lessons, item.id, {
          ...item,
          content: {
            ...item.content
          }
        })
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
    },

    setTestimonies (state, items) {
      items.forEach(item => {
        Vue.set(state.testimonies, item.id, {
          ...item,
          content: {
            ...item.content
          }
        })
      })
    },
    setTestimoniesByLesson (state, items) {
      items.forEach(item => {
        Vue.set(state.testimonyByLesson, item.lesson_id, item.id)
      })
    },
    setTestimoniesByKey (state, { key, items }) {
      if (items.length === 0) {
        Vue.set(state.testimoniesIdsByKey, key, [])
      }
      items.forEach(item => {
        if (state.testimoniesIdsByKey[key] == null) {
          Vue.set(state.testimoniesIdsByKey, key, [])
        }
        if (state.testimoniesIdsByKey[key].indexOf(item.id) === -1) {
          state.testimoniesIdsByKey[key].push(item.id)
        }
      })
    }
  },
  getters: {
    identity: (_1, _2, _3, rootGetters) => rootGetters['site/identity'],
    getUI: state => state.UI,
    getUILangLocale: state => state.UI.locale,
    getUIIsDarkMode: state => state.UI.isDarkMode,
    getUILangCode: (state, getters) => {
      const lang = state.UI.locales.find(l => l.locale === getters.getUILangLocale)
      return lang?.code || 'en'
    },

    getLesson: state => id => state.lessons[id] || null,
    getLessonsIdsByDate: state => date => state.lessonsIdsByDate[date] || [],
    getLessonsIdsByMonth: state => date => state.lessonsIdsByMonth[date] || [],
    getLessonsIdsByChapter: state => chapter => state.lessonsIdsByChapter[chapter] || [],

    getLessonsByDate: (state, getters) => date => getters.getLessonsIdsByDate(date).map(id => getters.getLesson(id)),
    getLessonsByMonth: (state, getters) => date => getters.getLessonsIdsByMonth(date).map(id => getters.getLesson(id)),
    getLessonsByChapter: (state, getters) => chapter => getters.getLessonsIdsByChapter(chapter).map(id => getters.getLesson(id)),

    isLoadingLessonById: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('loadLessonById', id),
    isLoadingLessonsByDate: (_1, _2, _3, rootGetters) => date => rootGetters['loading/has']('loadLessonsByDate', date),
    isLoadingLessonsByMonth: (_1, _2, _3, rootGetters) => date => rootGetters['loading/has']('loadLessonsByMonth', date),
    isLoadingLessonsByChapter: (_1, _2, _3, rootGetters) => chapter => rootGetters['loading/has']('loadLessonsByChapter', chapter),
    isMarkingLessonAsRead: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('markLessonAsRead', id),

    getTestimony: state => id => state.testimonies[id] || null,
    getTestimonyByLesson: (state, getters) => id => state.testimonyByLesson[id] ? getters.getTestimony(state.testimonyByLesson[id]) : null,
    getTestimoniesIdsByKey: state => key => state.testimoniesIdsByKey[key] || [],
    getTestimoniesByKey: (state, getters) => key => getters.getTestimoniesIdsByKey(key).map(id => getters.getTestimony(id)),
    getTestimoniesLatest: (state, getters) => getters.getTestimoniesByKey('latest'),
    getTestimoniesByLesson: (state, getters) => lesson => getters.getTestimoniesByKey('lesson' + lesson),

    isSavingTestimony: (_1, _2, _3, rootGetters) => rootGetters['loading/has']('saveTestimony'),
    isPublishingTestimony: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('publishTestimony', id),
    isLoadingTestimonyById: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('loadTestimonyById', id),
    isLoadingTestimonyByLesson: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('loadTestimonyByLesson', id),
    isLoadingTestimoniesByLessonId: (_1, _2, _3, rootGetters) => id => rootGetters['loading/has']('loadTestimoniesByLessonId', id),
    isLoadingTestimoniesLatest: (_1, _2, _3, rootGetters) => rootGetters['loading/has']('loadTestimoniesLatest')

  },
  actions: {
    beforeunload () {

    },
    bootstrap ({ dispatch, commit }, data) {
      dispatch('site/setIdentity', data.user)
      commit('setUIProp', { prop: 'locales', value: data.locales || [] })
      commit('setUIProp', { prop: 'locale', value: data.locale || 'en' })
    },
    login ({ commit }) {
      commit('resetUserState')
    },
    logout ({ commit }) {
      commit('resetUserState')
    },
    async loadLessonById ({ dispatch, commit, getters }, id) {
      if (getters.isLoadingLessonById(id)) {
        return null
      }
      dispatch('loading/start', `loadLessonById/${id}`, { root: true })
      const { data, err } = await LessonsService.view(id)
      if (err) {
        console.error(err)
      } else {
        commit('setLessons', [data])
        commit('setLessonsIdsByDate', [data])
      }
      dispatch('loading/end', `loadLessonById/${id}`, { root: true })
    },
    async loadLessonsByDate ({ dispatch, commit, getters }, date) {
      if (getters.isLoadingLessonsByDate(date)) {
        return null
      }
      dispatch('loading/start', `loadLessonsByDate/${date}`, { root: true })
      try {
        const { data } = await LessonsService.byDate(date)
        commit('setLessons', data)
        commit('setLessonsIdsByDate', data)
      } catch (err) {
        console.error(err)
      }
      dispatch('loading/end', `loadLessonsByDate/${date}`, { root: true })
    },
    async loadLessonsByMonth ({ dispatch, commit, getters }, date) {
      if (getters.isLoadingLessonsByMonth(date)) {
        return null
      }
      dispatch('loading/start', `loadLessonsByMonth/${date}`, { root: true })
      try {
        const { data } = await LessonsService.byMonth(date + '-01')
        commit('setLessons', data)
        commit('setLessonsIdsByDate', data)
        commit('setLessonsIdsByMonth', data)
      } catch (err) {
        console.error(err)
      }
      dispatch('loading/end', `loadLessonsByMonth/${date}`, { root: true })
    },
    async loadLessonsByChapter ({ dispatch, commit, getters }, chapter) {
      if (getters.isLoadingLessonsByChapter(chapter)) {
        return null
      }
      dispatch('loading/start', `loadLessonsByChapter/${chapter}`, { root: true })
      try {
        const { data } = await LessonsService.byChapter(chapter)
        commit('setLessons', data)
        commit('setLessonsIdsByChapter', { chapter, items: data })
      } catch (err) {
        console.error(err)
      }
      dispatch('loading/end', `loadLessonsByChapter/${chapter}`, { root: true })
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

    async publishTestimony ({ dispatch, commit, getters }, { id, isPublished }) {
      if (getters.isPublishingTestimony(id)) {
        return null
      }
      dispatch('loading/start', `publishTestimony/${id}`, { root: true })

      const { data, err } = await TestimoniesService.edit(id, {
        is_published: isPublished ? 1 : 0
      })
      if (err) throw err

      commit('setLessons', [data].filter(d => d.lesson).map(d => d.lesson))
      commit('setTestimonies', [data])

      dispatch('loading/end', `publishTestimony/${id}`, { root: true })
    },
    async saveTestimony ({ dispatch, commit, getters }, formData) {
      if (getters.isSavingTestimony) {
        return null
      }
      dispatch('loading/start', 'saveTestimony', { root: true })

      let response = { data: null, err: null }
      if (formData.id) {
        const { data, err } = await TestimoniesService.edit(formData.id, formData)
        response = { data, err }
      } else {
        const { data, err } = await TestimoniesService.create(formData)
        response = { data, err }
      }

      if (response.err) throw response.err

      commit('setLessons', [response.data].filter(d => d.lesson).map(d => d.lesson))
      commit('setTestimonies', [response.data])
      commit('setTestimoniesByLesson', [response.data])

      dispatch('loading/end', 'saveTestimony', { root: true })

      return response
    },
    async loadTestimonyById ({ dispatch, commit, getters }, id) {
      if (getters.isLoadingTestimonyById(id)) {
        return null
      }
      dispatch('loading/start', `loadTestimonyById/${id}`, { root: true })
      try {
        const { data } = await TestimoniesService.view(id)
        commit('setLessons', [data].filter(d => d.lesson).map(d => d.lesson))
        commit('setTestimonies', [data])
      } catch (err) { console.error(err) }
      dispatch('loading/end', `loadTestimonyById/${id}`, { root: true })
    },
    async loadTestimonyByLesson ({ dispatch, commit, getters }, id) {
      if (getters.isLoadingTestimonyByLesson(id)) {
        return null
      }
      dispatch('loading/start', `loadTestimonyByLesson/${id}`, { root: true })
      const { data, err } = await TestimoniesService.viewByLesson(id)
      if (err) {
        dispatch('addError', err)
      } else {
        commit('setLessons', [data].filter(d => d.lesson).map(d => d.lesson))
        commit('setTestimonies', [data])
        commit('setTestimoniesByLesson', [data])
      }
      dispatch('loading/end', `loadTestimonyByLesson/${id}`, { root: true })
    },
    async loadTestimoniesByLesson ({ dispatch, commit, getters }, id) {
      if (getters.isLoadingTestimoniesByLessonId(id)) {
        return null
      }
      dispatch('loading/start', `loadTestimoniesByLessonId/${id}`, { root: true })
      const { data, err } = await TestimoniesService.listByLesson(id)
      if (err) {
        dispatch('addError', err)
      } else {
        commit('setLessons', data.filter(d => d.lesson).map(d => d.lesson))
        commit('setTestimonies', data)
        commit('setTestimoniesByKey', { key: `lesson${id}`, items: data })
        commit('setTestimoniesByLesson', data.filter(d => d.created_by === getters.identity.id))
      }
      dispatch('loading/end', `loadTestimoniesByLessonId/${id}`, { root: true })
    },

    addError ({ commit }, error) {
      commit('addError', error)
    },
    uiShowLangSelector ({ commit }) {
      commit('setUIProp', { prop: 'showLangSelector', value: true })
    },
    uiCloseLangSelector ({ commit }) {
      commit('setUIProp', { prop: 'showLangSelector', value: false })
    },
    setUILangCode ({ commit, getters }, code) {
      const lang = getters.getUI.locales.find(l => l.code === code)
      if (lang) {
        commit('setUIProp', { prop: 'locale', value: lang.locale })
        SiteService.setLocale(lang.locale).then(() => {
          window.location.reload()
        })
      }
    },
    setUIIsDarkMode ({ commit }, is) {
      commit('setUIProp', { prop: 'isDarkMode', value: is })
    }
  },
  modules: {
    loading,
    site
  },
  strict: debug,
  plugins: debug ? [createLogger()] : []
})
