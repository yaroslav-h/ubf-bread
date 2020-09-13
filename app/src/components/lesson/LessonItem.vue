<template>
  <router-link :to="{name: 'Lesson', params: {id:lesson.id}}" class="lesson-item d-flex align-items-center">
    <div class="date mr-3 text-center">
      <div>{{ formattedDay }}</div>
      <div class="ws-nw">{{ formattedMonYear }}</div>
    </div>
    <div class="w-100">
      <div>
        <div class="h6 mb-0"><fa v-if="lesson.is_intro" icon="play-circle" class="mr-2"/>{{ lesson.title }}</div>
        <div><fa icon="book-open" class="mr-2"/>{{lesson.passage}}</div>
      </div>
    </div>
    <div class="d-flex align-items-center opts">
      <div>
        <div class="d-flex ws-nw">
          <div v-if="lesson.is_passed"><fa icon="check-circle"/> Passed</div>
          <div v-else-if="lesson.is_read"><fa icon="check"/> Read</div>

          <div class="ml-2"><fa icon="eye"/> {{lesson.user_reads_count}}</div>
          <div class="ml-2"><fa icon="paper-plane"/> {{lesson.testimonies_count}}</div>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script>
import moment from 'moment'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCheck, faCheckCircle, faQuestion } from '@fortawesome/free-solid-svg-icons'
library.add(faCheck, faCheckCircle, faQuestion)

export default {
  name: 'LessonItem',
  props: {
    lesson: {
      required: true
    }
  },
  computed: {
    formattedDay () {
      return moment(this.lesson.date).format('DD')
    },
    formattedMonYear () {
      return moment(this.lesson.date).format('MMM yyyy')
    }
  }
}
</script>

<style lang="scss" scoped>
.lesson-item {
  text-decoration: none !important;
  .date, .opts {
    color: #343434;
  }
}
</style>
