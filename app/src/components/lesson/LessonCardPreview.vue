<template>
  <div class="card" :class="{collapsed: collapsed}">
    <div class="card-header d-flex align-items-center">
      <div class="mr-3 text-center" v-if="showDay">
        <div>{{getDay}}</div>
        <div>{{getDayName}}</div>
      </div>
      <div class="w-100">
        <div class="h5"><fa v-if="lesson.is_intro" icon="play-circle" class="mr-2"/>{{ lesson.title }}</div>
        <div class="passage"><a :href="lesson.passage_link" target="_blank"><fa icon="book-open" class="mr-2"/>{{lesson.passage}}</a></div>
      </div>
    </div>
    <div class="card-body">
      <div v-if="lesson.content.key_verse">{{lesson.content.key_verse}}</div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center" v-if="!collapsed">
      <lesson-counters :lesson="lesson"/>
      <div>
        <router-link class="btn btn-link" :to="{name: 'Lesson', params: {id:lesson.id}}">
          {{ $t('read') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlayCircle, faBookOpen, faEye, faTimes, faPaperPlane, faCheckCircle } from '@fortawesome/free-solid-svg-icons'
import LessonCounters from '@/components/lesson/LessonCounters'
library.add(faPlayCircle, faBookOpen, faEye, faTimes, faPaperPlane, faCheckCircle)

export default {
  name: 'LessonCardPreview',
  components: { LessonCounters },
  props: {
    lesson: {
      required: true
    },
    collapsed: {
      required: false,
      default: false
    },
    showEye: {
      required: false,
      default: false
    },
    showDay: {
      required: false,
      default: false
    }
  },
  computed: {
    isLoggedIn () {
      return this.$store.getters['site/isLoggedIn']
    },
    getDay () {
      return moment(this.lesson.date).format('DD')
    },
    getDayName () {
      return moment(this.lesson.date).format('ddd')
    },
    isMarkingAsRead () {
      return this.$store.getters.isMarkingLessonAsRead(this.lesson.id)
    }
  },
  methods: {
    onMarkAsRead () {
      this.$store.dispatch('markLessonAsRead', this.lesson.id)
    }
  }
}
</script>

<style scoped>
.passage {
  font-size: 14px;
}
</style>
