<template>
  <div class="d-flex">
    <div :class="{'text-muted': !lesson.is_read}">
      <fa v-if="!useLabels" icon="eye"/> {{lesson.user_reads_count}} <span v-if="useLabels">{{ $t('Reads') }}</span>
    </div>
    <div :class="{'text-muted': !lesson.is_passed}" class="ml-2">
      <fa v-if="!useLabels" icon="paper-plane"/> {{lesson.testimonies_count}} <span v-if="useLabels">{{ $t('Testimonies') }}</span>
    </div>
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
    },
    useLabels: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
.text-muted {
  color: #888888 !important;
}
.d-flex > div {
  white-space: nowrap;
}
</style>
