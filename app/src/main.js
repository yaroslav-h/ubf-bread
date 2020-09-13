import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import axios from 'axios'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import i18n from './i18n'
import moment from 'moment'
Vue.component('fa', FontAwesomeIcon)

Vue.config.productionTip = false

if (process.env.NODE_ENV !== 'development') {
  Vue.config.errorHandler = (err, vm, info) => {
    console.error(err)
    store.dispatch('addError', {
      scope: 'vue', info, error: err
    })
  }
  window.onerror = function (message, source, lineno, colno, error) {
    store.dispatch('addError', {
      scope: 'window', info: 'global', error, message, source, lineno, colno
    })
  }
  window.addEventListener('unhandledrejection', function (event) {
    store.dispatch('addError', {
      scope: 'window', info: 'promise', error: event.reason, event
    })
  })
}

window.addEventListener('beforeunload', () => {
  store.dispatch('beforeunload')
})

const bootstrap = (state) => {
  axios.defaults.headers.common['X-CSRF-Token'] = state?.csrf
  store.dispatch('bootstrap', state)
  i18n.locale = store.getters.getUILangCode
  moment.locale(i18n.locale)
  new Vue({
    i18n,
    router,
    store,
    render: h => h(App)
  }).$mount('#app')
}

if (process.env.NODE_ENV === 'development') {
  axios.get('/_/site/initial-state', { params: { path: window.location.pathname } })
    .then(({ data }) => bootstrap(data))
} else {
  if (window._sharedData == null || window._sharedData.check === true) {
    axios.get('/_/site/initial-state', { params: { path: window.location.pathname } })
      .then(({ data }) => bootstrap(data))
      .catch(() => bootstrap({}))
  } else {
    bootstrap(window._sharedData)
  }
}
