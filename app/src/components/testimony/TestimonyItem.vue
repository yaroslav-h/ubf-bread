<template>
  <router-link :to="{name: 'LessonTestimony', params: {lesson:testimony.lesson_id, testimony:testimony.id}}" class="testimony-item d-flex align-items-center">
    <!--<div class="w-100">
      <div>
        <div class="h6 mb-0"><fa v-if="lesson.is_intro" icon="play-circle" class="mr-2"/>{{ lesson.title }}</div>
        <div class="passage"><fa icon="book-open" class="mr-2"/>{{lesson.passage}}</div>
      </div>
    </div>-->
    <div class="d-flex align-items-center opts">
      <div class="dt">
        {{ testimony.createdBy.name }} / {{ formattedUpdatedAt }}
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
  name: 'TestimonyItem',
  props: {
    testimony: {
      required: true
    }
  },
  computed: {
    formattedUpdatedAt () {
      return moment(this.testimony.updated_at * 1000).format('DD MMMM YYYY, HH:mm')
    }
  }
}
</script>

<style lang="scss" scoped>
.testimony-item {
  text-decoration: none !important;
}

@media (max-width: 767px) {

}
</style>
