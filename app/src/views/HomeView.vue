<template>
  <main-layout>

    <base-page-header>
      <div class="d-flex justify-content-center">
        <div>{{ formattedDate(getDate) }}</div>
      </div>
    </base-page-header>

    <base-page-body :is-loading="isLoadingLessons && !getLessons.length" :not-found="!isLoadingLessons && !getLessons.length">
      <lesson-card v-for="lesson in getLessons" :key="lesson.id" :lesson="lesson" class="mt-2"/>
    </base-page-body>

  </main-layout>
</template>

<script>
import moment from 'moment'
import MainLayout from '@/views/layouts/MainLayout'
import { yyyyMmDd } from '@/helpers/momentHelper'
import LessonCard from '@/components/lesson/LessonCard'
import BasePageBody from '@/components/base/page/BasePageBody'
import BasePageHeader from '@/components/base/page/BasePageHeader'

export default {
  name: 'HomeView',
  components: { BasePageHeader, BasePageBody, LessonCard, MainLayout },
  computed: {
    getDate () {
      return yyyyMmDd()
    },
    formattedDate () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    getLessons () {
      return this.$store.getters.getLessonsByDate(this.getDate)
    },
    isLoadingLessons () {
      return this.$store.getters.isLoadingLessonsByDate(this.getDate)
    }
  },
  mounted () {
    this.$store.dispatch('loadLessonsByDate', this.getDate)
  }
}
</script>
