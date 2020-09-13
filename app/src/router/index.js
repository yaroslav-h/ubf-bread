import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'
import HomeView from '@/views/HomeView.vue'

Vue.use(VueRouter)

const isLoggedIn = () => store.getters['site/isLoggedIn']

const guestRequired = (to, from, next) => {
  next((isLoggedIn() ? { name: 'Home' } : {}))
}
const loginRequired = (to, from, next) => {
  next((isLoggedIn() ? {} : { name: 'Login' /* query: {redirect: to.fullPath} */ }))
}

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomeView
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: () => import('@/views/CalendarView.vue')
  },
  {
    path: '/search',
    name: 'Search',
    component: () => import('@/views/SearchView.vue')
  },
  {
    path: '/archive',
    name: 'Archive',
    component: () => import('@/views/ArchiveView.vue')
  },
  {
    path: '/archive/:chapter',
    name: 'ArchiveChapter',
    component: () => import('@/views/ArchiveView.vue')
  },
  {
    path: '/lesson/:id',
    name: 'Lesson',
    component: () => import('@/views/LessonView')
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/views/ProfileView.vue'),
    beforeEnter: loginRequired
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/LoginView'),
    beforeEnter: guestRequired
  }
]

const router = new VueRouter({
  mode: 'history',
  scrollBehavior (to, from, savedPosition) {
    if (savedPosition) {
      to.meta.has_saved_scroll_pos = true
      return savedPosition
    } else {
      to.meta.has_saved_scroll_pos = false
      return { x: 0, y: 0 }
    }
  },
  base: process.env.BASE_URL,
  routes
})

export default router
