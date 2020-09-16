<template>
  <main-layout>

    <base-page-header :has-back-btn="true" v-if="getLesson">
      {{ formattedDate(getLesson.date) }}
    </base-page-header>

    <base-page-body :is-loading="isLoading && getLesson == null" :not-found="!isLoading && getLesson == null">

      <lesson-card class="mt-2" v-if="getLesson" :lesson="getLesson"/>

    </base-page-body>

  </main-layout>
</template>

<script>
import MainLayout from '@/views/layouts/MainLayout'
import BasePageHeader from '@/components/base/page/BasePageHeader'
import BasePageBody from '@/components/base/page/BasePageBody'
import LessonCard from '@/components/lesson/LessonCard'
import moment from 'moment'
export default {
  name: 'LessonView',
  components: { LessonCard, BasePageBody, BasePageHeader, MainLayout },
  data () {
    return {
      id: null
    }
  },
  computed: {
    formattedDate () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    isLoading () {
      return this.$store.getters.isLoadingLessonById(this.id)
    },
    getLesson () {
      return this.$store.getters.getLesson(this.id)
    }
  },
  methods: {
    onLoad (route) {
      this.id = route.params.id
      this.$store.dispatch('loadLessonById', route.params.id)
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.onLoad(to))
  },
  beforeRouteUpdate (to, from, next) {
    next()
    this.$nextTick(() => this.onLoad(to))
  },
  mounted () {
    this.onLoad(this.$route)
  }
}
</script>

<style scoped>

</style>
