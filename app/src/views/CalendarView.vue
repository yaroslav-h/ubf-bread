<template>
  <main-layout>

    <base-page-body class="mt-2">
      <div>
        <v-calendar @dayclick="onDayClick" @update:to-page="onToPage" is-expanded :min-date="minDate" :attributes="attributes">
          <!--<div slot="day-content" slot-scope="{day}">
            <div class="text-center">
              {{ day.day }}
            </div>
          </div>-->
        </v-calendar>
      </div>

      <div v-if="isLoadingLessons && getLessons.length === 0" class="text-center p-3 py-5"><base-fa-spinner/></div>

      <div v-if="showForDate" class="text-center mt-2 p-2 d-flex justify-content-center align-items-center">
        {{formattedDate(showForDate)}} <button class="btn btn-sm btn-link ml-2" @click="showForDate = null"><fa icon="times"/></button>
      </div>

      <div class="mt-2">
        <lesson-card v-for="lesson in getLessonsList"
                     :collapsed="show !== lesson.id" :key="lesson.id"
                     :lesson="lesson" :show-day="true" :show-eye="true"
                     @open="show = lesson.id" @close="show = null"
                     class="mt-2"/>
      </div>

    </base-page-body>

  </main-layout>
</template>

<script>
import moment from 'moment'
import MainLayout from '@/views/layouts/MainLayout'
import Calendar from 'v-calendar/lib/components/calendar.umd'
import BasePageBody from '@/components/base/page/BasePageBody'
import LessonCard from '@/components/lesson/LessonCard'
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
library.add(faTimes)

export default {
  name: 'Calendar',
  components: { BaseFaSpinner, LessonCard, BasePageBody, MainLayout, vCalendar: Calendar },
  data () {
    return {
      minDate: new Date(2000, 0, 1),
      page: null,
      show: null,
      showForDate: null
    }
  },
  computed: {
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

          return {
            key: l.id,
            bar: color,
            dates: d.toDate()
          }
        })
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
