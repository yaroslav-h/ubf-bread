<template>
  <main-layout>

    <base-page-body class="mt-2">
      <div>
        <v-calendar :locale="$store.getters.getUILangCode" @dayclick="onDayClick" @update:to-page="onToPage" is-expanded :min-date="minDate" :attributes="attributes">
          <!--<div slot="day-content" slot-scope="{day}">
            <div class="text-center">
              {{ day.day }}
            </div>
          </div>-->
        </v-calendar>
      </div>
    </base-page-body>
    <base-page-body :is-loading="isLoadingLessons && getLessons.length === 0">
      <div v-if="showForDate" class="text-center mt-2 p-2 d-flex justify-content-center align-items-center">
        {{formattedDate(showForDate)}} <button class="btn btn-sm btn-link ml-2" @click="showForDate = null"><fa icon="times"/></button>
      </div>
      <div class="list-group mt-2">
        <lesson-item class="list-group-item" v-for="lesson in getLessonsList" :key="lesson.id" :lesson="lesson"/>
      </div>

    </base-page-body>

  </main-layout>
</template>

<script>
import moment from 'moment'
import MainLayout from '@/views/layouts/MainLayout'
import Calendar from 'v-calendar/lib/components/calendar.umd'
import BasePageBody from '@/components/base/page/BasePageBody'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import LessonItem from '@/components/lesson/LessonItem'
library.add(faTimes)

export default {
  name: 'Calendar',
  components: { LessonItem, BasePageBody, MainLayout, vCalendar: Calendar },
  data () {
    return {
      minDate: new Date(2000, 0, 1),
      page: null,
      showForDate: null
    }
  },
  computed: {
    isLoggedIn () {
      return this.$store.getters['site/isLoggedIn']
    },
    user () {
      return this.$store.getters['site/identity']
    },
    formattedDate () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    getYearMonth () {
      return this.page ? this.page.year + '-' + `${this.page.month}`.padStart(2, '0') : null
    },
    getLessons () {
      return this.$store.getters.getLessonsByMonth(this.getYearMonth)
    },
    getLessonsList () {
      if (this.showForDate) {
        return this.$store.getters.getLessonsByMonth(this.getYearMonth).filter(l => {
          return l.date === this.showForDate
        })
      }

      return this.$store.getters.getLessonsByMonth(this.getYearMonth)
    },
    isLoadingLessons () {
      return this.$store.getters.isLoadingLessonsByMonth(this.getYearMonth)
    },
    attributes () {
      const today = moment().startOf('day')
      let userCreatedAt = null

      if (!this.isLoggedIn) {
        return [
          {
            key: 'today',
            bar: 'blue',
            dates: new Date()
          }
        ]
      } else {
        userCreatedAt = moment(this.user.created_at * 1000)
      }

      return [
        {
          key: 'today',
          bar: 'blue',
          dates: new Date()
        },
        ...this.getLessons.map(l => {
          const d = moment(l.date)

          let color = false
          if (d.isSame(today)) {
            color = 'blue'
          } else {
            color = l.is_passed ? 'green' : (l.is_read ? 'yellow' : (d.isSameOrAfter(today) ? false : 'red'))
          }

          if (userCreatedAt && !d.isSameOrAfter(userCreatedAt) && color === 'red') {
            return null
          }

          return {
            key: l.id,
            bar: color,
            dates: d.toDate()
          }
        }).filter(d => d != null)
      ]
    }
  },
  watch: {
    getYearMonth (date) {
      this.$store.dispatch('loadLessonsByMonth', date)
    }
  },
  methods: {
    onToPage (page) {
      this.page = page
    },
    onDayClick (e) {
      this.showForDate = e.id
    }
  }
}
</script>

<style scoped>

</style>
