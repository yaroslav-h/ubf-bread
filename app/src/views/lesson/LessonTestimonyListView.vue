<template>
  <main-layout>
    <base-page-header :has-back-btn="true" v-if="getLesson">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <router-link :to="{name: 'Lesson', params: {id:getLesson.id}}">{{ $t('Lesson') }}</router-link> / {{ $t('Testimonies') }}
        </div>
        <div>
          <router-link v-if="getLesson.is_read && getLesson.passed_id" :to="{name: 'LessonTestimonyMy', params: {id: getLesson.id}}"
                       class="btn btn-sm btn-primary">
            <fa v-if="!getLesson.is_passed" icon="edit" class="mr-1"/>
            {{ !getLesson.is_passed ? $t('Edit') : $t('View') }}
            <fa v-if="getLesson.is_passed" icon="check-circle" class="ml-1"/>
          </router-link>
          <router-link v-else-if="getLesson.is_read" :to="{name: 'LessonTestimonyMyEdit', params: {id: getLesson.id}}"
                       class="btn btn-sm btn-primary">
            <fa icon="plus-circle" class="mr-1"/> {{ $t('Add') }}
          </router-link>
        </div>
      </div>
    </base-page-header>

    <base-page-body :is-loading="isLoading && (getLesson == null || !getTestimonies.length)" :not-found="!isLoading && getLesson == null">

      <div v-if="!isLoading && getLesson && getLesson.testimonies_count === 0 && getTestimonies.length === 0" class="text-center p-3">
        {{ $t('The list is empty') }}
      </div>
      <div v-if="!isLoading && getLesson && getLesson.testimonies_count > 0 && getLesson.testimonies_count !== getTestimonies.length" class="text-center p-3 card mt-2 mb-2">
        {{ $t('There are some testimonies that being written') }}
      </div>

      <div class="list-group">
        <testimony-item v-for="item in getTestimonies" :testimony="item" :key="item.id" class="list-group-item"/>
      </div>

    </base-page-body>
  </main-layout>
</template>

<script>
import BasePageHeader from '@/components/base/page/BasePageHeader'
import BasePageBody from '@/components/base/page/BasePageBody'
import moment from 'moment'
import MainLayout from '@/views/layouts/MainLayout'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCheckCircle, faEdit, faPlusCircle } from '@fortawesome/free-solid-svg-icons'
import TestimonyItem from '@/components/testimony/TestimonyItem'
library.add(faCheckCircle, faEdit, faPlusCircle)

export default {
  name: 'LessonTestimonyListView',
  components: { TestimonyItem, MainLayout, BasePageBody, BasePageHeader },
  data () {
    return {
      id: null
    }
  },
  computed: {
    user () {
      return this.$store.getters['site/identity']
    },
    formattedDate () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    formattedCreatedAt () {
      return date => moment(date).format('DD MMMM YYYY, HH:mm')
    },
    isLoading () {
      return this.$store.getters.isLoadingLessonById(this.id) || this.$store.getters.isLoadingTestimoniesByLessonId(this.id)
    },
    getLesson () {
      return this.$store.getters.getLesson(this.id)
    },
    getTestimonies () {
      return this.$store.getters.getTestimoniesByLesson(this.id)
    }
  },
  methods: {
    onLoad (route) {
      this.id = route.params.id
      this.$store.dispatch('loadLessonById', route.params.id)
      this.$store.dispatch('loadTestimoniesByLesson', route.params.id)
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
