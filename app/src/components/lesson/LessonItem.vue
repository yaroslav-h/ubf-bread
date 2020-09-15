<template>
  <router-link :to="{name: 'Lesson', params: {id:lesson.id}}" class="lesson-item d-flex align-items-center">
    <div class="date mr-3 text-center">
      <div>{{ formattedDay }}</div>
      <div class="ws-nw">{{ formattedMonYear }}</div>
    </div>
    <div class="w-100">
      <div>
        <div class="h6 mb-0"><fa v-if="lesson.is_intro" icon="play-circle" class="mr-2"/>{{ lesson.title }}</div>
        <div class="passage"><fa icon="book-open" class="mr-2"/>{{lesson.passage}}</div>
      </div>
    </div>
    <div class="d-flex align-items-center opts">
      <div class="dt">
        {{lesson.passage}} / <b>{{ formattedDay }}</b> <small>{{ formattedMonYear }}</small>
      </div>
      <lesson-counters :lesson="lesson"/>
    </div>
  </router-link>
</template>

<script>
import moment from 'moment'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCheck, faCheckCircle, faQuestion } from '@fortawesome/free-solid-svg-icons'
import LessonCounters from '@/components/lesson/LessonCounters'
library.add(faCheck, faCheckCircle, faQuestion)

export default {
  name: 'LessonItem',
  components: { LessonCounters },
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
  .h6 {
    font-size: 18px;
  }
  .passage {
    font-size: 14px;
  }
  .opts {
    font-size: 14px;
    .dt {
      display: none;
    }
  }
}

@media (max-width: 767px) {
  .lesson-item {
    flex-direction: column;
    align-items: flex-start !important;

    .date {
      display: none;
    }
    .h6 {
      font-size: 16px;
    }
    .passage {
      display: none;
    }
    .opts {
      font-size: 14px;
      justify-content: space-between;
      width: 100%;
      .dt {
        display: block;
      }
      .st > span {
        display: none;
      }
    }
  }
}
</style>
