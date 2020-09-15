<template>
  <div class="d-flex">
    <div :class="{'text-muted': !lesson.is_read}"><fa icon="eye"/> {{lesson.user_reads_count}}</div>
    <div :class="{'text-muted': !lesson.is_passed}" class="ml-2"><fa icon="paper-plane"/> {{lesson.testimonies_count}}</div>
  </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEye, faTimes } from '@fortawesome/free-solid-svg-icons'
library.add(faEye, faTimes)

export default {
  name: 'LessonCounters',
  props: {
    lesson: {
      required: true
    }
  }
}
</script>

<style scoped>
.text-muted {
  color: #888888 !important;
}
</style>
