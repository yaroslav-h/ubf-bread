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
      <div v-if="showEye">
        <button class="btn btn-link" @click="$emit('open')" v-if="collapsed"><fa icon="eye"/></button>
        <button class="btn btn-link" @click="$emit('close')" v-else><fa icon="times"/></button>
      </div>
    </div>
    <div class="card-body">
      <div v-if="lesson.content.key_verse">{{lesson.content.key_verse}}</div>
      <div v-if="!collapsed" class="mt-3" v-html="lesson.content.body"></div>
      <div v-if="!collapsed && lesson.content.prayer"><strong>Prayer:</strong> {{ lesson.content.prayer }}</div>
      <div v-if="!collapsed && lesson.content.one_word"><strong>One word:</strong> {{ lesson.content.one_word }}</div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center" v-if="!collapsed">
      <lesson-counters :lesson="lesson"/>
      <div v-if="isLoggedIn">

        <router-link v-if="lesson.is_read && lesson.passed_id" :to="{name: 'LessonTestimonyView', params: {id: lesson.id}}"
                class="btn" :class="{'btn-primary': !lesson.is_passed, 'btn-default': lesson.is_passed}">
          {{ $t('Testimony') }}
          <fa v-if="lesson.is_passed" icon="check-circle" class="ml-1"/>
          <fa v-else icon="edit" class="ml-1"/>
        </router-link>
        <router-link v-else :to="{name: 'LessonTestimonyEditView', params: {id: lesson.id}}"
                class="btn btn-primary">
          <fa icon="plus-circle" class="mr-1"/> {{ $t('Testimony') }}
        </router-link>

        <button v-if="!lesson.is_read" :disabled="isMarkingAsRead || lesson.is_read" @click="onMarkAsRead"
                class="btn" :class="{'btn-primary': !lesson.is_read, 'btn-default': lesson.is_read}">
          {{ $t('Mark as read') }}<base-fa-spinner v-if="isMarkingAsRead" class="ml-2"/>
        </button>

      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
import LessonCounters from '@/components/lesson/LessonCounters'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlayCircle, faBookOpen, faEye, faTimes, faPaperPlane, faCheckCircle, faEdit, faPlusCircle } from '@fortawesome/free-solid-svg-icons'
library.add(faPlayCircle, faBookOpen, faEye, faTimes, faPaperPlane, faCheckCircle, faEdit, faPlusCircle)

export default {
  name: 'LessonCard',
  components: { LessonCounters, BaseFaSpinner },
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
