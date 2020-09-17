<template>
  <main-layout>

    <base-page-header :has-back-btn="true" v-if="getLesson">
      <router-link :to="{name: 'LessonTestimonies', params: {id:getLesson.id}}">{{ $t('Testimonies') }}</router-link> / {{ $t('View') }}
    </base-page-header>

    <base-page-body :is-loading="isLoading && (getLesson == null || getTestimony == null)" :not-found="!isLoading && getLesson == null">

      <div class="card mt-2" v-if="getLesson && getTestimony">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div>{{getLesson.title}}</div>
              <div class="small">{{formattedDate(getLesson.date)}}</div>
            </div>
            <div>
              <base-passage-link :src="getLesson.passage" :link="getLesson.passage_link"/>
            </div>
          </div>
        </div>
        <div class="pt-3 px-3 pb-0">
          {{ getLesson.content.key_verse }}
        </div>

        <div class="card-body">
          <div class="editor__content" v-html="getTestimony.content.body"></div>
          <div v-if="getTestimony.content.prayer"><strong>Prayer:</strong> {{ getTestimony.content.prayer }}</div>
          <div v-if="getTestimony.content.one_word"><strong>One word:</strong> {{ getTestimony.content.one_word }}</div>
        </div>
        <div class="card-footer">
          <span><fa icon="user" class="mr-2"/>{{getTestimony.createdBy.name}}</span>
          <span><fa icon="clock" class="ml-2 mr-2"/>{{ formattedCreatedAt(getTestimony.created_at * 1000) }}</span>
        </div>
      </div>

    </base-page-body>

  </main-layout>
</template>

<script>
import moment from 'moment'
import MainLayout from '@/views/layouts/MainLayout'
import BasePageHeader from '@/components/base/page/BasePageHeader'
import BasePageBody from '@/components/base/page/BasePageBody'
import BasePassageLink from '@/components/base/BasePassageLink'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUser, faClock } from '@fortawesome/free-solid-svg-icons'
library.add(faUser, faClock)

export default {
  name: 'LessonTestimonyView',
  components: { BasePassageLink, BasePageBody, BasePageHeader, MainLayout },
  data () {
    return {
      lessonId: null,
      testimonyId: null
    }
  },
  computed: {
    formattedDate () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    formattedCreatedAt () {
      return date => moment(date).format('DD MMMM YYYY, HH:mm')
    },
    isLoading () {
      return this.$store.getters.isLoadingTestimonyById(this.testimonyId)
    },
    getLesson () {
      return this.$store.getters.getLesson(this.lessonId)
    },
    getTestimony () {
      return this.$store.getters.getTestimony(this.testimonyId)
    }
  },
  methods: {
    onLoad (route) {
      this.lessonId = route.params.lesson
      this.testimonyId = route.params.testimony
      this.$store.dispatch('loadTestimonyById', route.params.testimony)
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.onLoad(to))
  },
  beforeRouteUpdate (to, from, next) {
    this.onLoad(to)
    next()
  },
  mounted () {
    this.onLoad(this.$route)
  }
}
</script>

<style scoped>

</style>
