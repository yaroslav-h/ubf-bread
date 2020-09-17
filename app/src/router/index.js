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
    component: () => import('@/views/CalendarView')
  },
  {
    path: '/search',
    name: 'Search',
    component: () => import('@/views/SearchView')
  },
  {
    path: '/archive',
    name: 'Archive',
    component: () => import('@/views/ArchiveView')
  },
  {
    path: '/archive/:chapter',
    name: 'ArchiveChapter',
    component: () => import('@/views/ArchiveView')
  },
  {
    path: '/lesson/:id',
    name: 'Lesson',
    component: () => import('@/views/lesson/LessonView')
  },
  {
    path: '/lesson/:id/testimonies',
    name: 'LessonTestimonies',
    component: () => import('@/views/lesson/LessonTestimonyListView'),
    beforeEnter: loginRequired
  },
  {
    path: '/lesson/:id/testimonies/my',
    name: 'LessonTestimonyMy',
    component: () => import('@/views/lesson/LessonTestimonyMyView'),
    beforeEnter: loginRequired
  },
  {
    path: '/lesson/:lesson/testimonies/:testimony',
    name: 'LessonTestimony',
    component: () => import('@/views/lesson/LessonTestimonyView'),
    beforeEnter: loginRequired
  },
  {
    path: '/lesson/:id/testimonies/my/edit',
    name: 'LessonTestimonyMyEdit',
    component: () => import('@/views/lesson/LessonTestimonyEditView'),
    beforeEnter: loginRequired
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('@/views/AboutView')
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/views/ProfileView'),
    beforeEnter: loginRequired
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/LoginView'),
    beforeEnter: guestRequired
  },
  {
    path: '/logout',
    name: 'Logout',
    component: () => import('@/views/auth/LogoutView'),
    beforeEnter: loginRequired
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
