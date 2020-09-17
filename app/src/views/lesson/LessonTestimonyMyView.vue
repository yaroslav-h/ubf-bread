<template>
  <main-layout>

    <base-page-header :has-back-btn="true" v-if="getLesson">
      <router-link :to="{name: 'LessonTestimonies', params: {id:getLesson.id}}">{{ $t('Testimonies') }}</router-link> / {{ $t('My') }}
    </base-page-header>

    <base-page-body :is-loading="isLoading && (getLesson == null || getTestimony == null)" :not-found="!isLoading && getLesson == null">

      <div v-if="getLesson && (!getTestimony && !isLoading)" class="text-center p-3">
        <div v-if="getLesson.is_read">
          <div>{{ $t('You have to add your testimony') }}</div>
          <div>
            <router-link :to="{name: 'LessonTestimonyMyEdit', params: {id: getLesson.id}}" class="btn btn-primary">
              {{ $t('Add') }}
            </router-link>
          </div>
        </div>
        <div v-else>
          <div>{{ $t('You have to read the lesson before') }}</div>
        </div>
      </div>

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

          <div v-if="getTestimony.created_by === user.id" class="d-flex align-items-center justify-content-between">
            <div>
              <span v-if="!getTestimony.is_published" class="badge badge-warning ml-2">
                {{ $t('Not published') }}
              </span>
              <span v-else>
                {{ getTestimony.createdBy.name }} / {{ formattedCreatedAt(getTestimony.updated_at * 1000) }}
              </span>
            </div>
            <div>
              <router-link :to="{name: 'LessonTestimonyMyEdit', params: {id: getTestimony.lesson_id}}" class="btn btn-default">
                {{ $t('Edit') }}
              </router-link>

              <button v-if="!getTestimony.is_published" class="btn btn-primary" @click="onPublish" :disabled="isPublishing">
                {{ $t('Publish') }}<base-fa-spinner v-if="isPublishing" class="ml-2"/>
              </button>

              <button v-if="getTestimony.is_published" class="btn btn-secondary" @click="onUnpublish" :disabled="isPublishing">
                {{ $t('Unpublish') }}<base-fa-spinner v-if="isPublishing" class="ml-2"/>
              </button>
            </div>
          </div>
          <div v-else>
            <span><fa icon="user" class="mr-2"/>{{getTestimony.createdBy.name}}</span>
            <span><fa icon="clock" class="ml-2 mr-2"/>{{ formattedCreatedAt(getTestimony.created_at * 1000) }}</span>
          </div>

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
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
library.add(faUser, faClock)

export default {
  name: 'LessonTestimonyMyView',
  components: { BaseFaSpinner, BasePassageLink, BasePageBody, BasePageHeader, MainLayout },
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
      return this.$store.getters.isLoadingLessonById(this.id) || this.$store.getters.isLoadingTestimonyByLesson(this.id)
    },
    isPublishing () {
      return this.getTestimony && this.$store.getters.isPublishingTestimony(this.getTestimony.id)
    },
    getLesson () {
      return this.$store.getters.getLesson(this.id)
    },
    getTestimony () {
      return this.$store.getters.getTestimonyByLesson(this.id)
    }
  },
  methods: {
    onLoad (route) {
      this.id = route.params.id
      this.$store.dispatch('loadLessonById', route.params.id)
      this.$store.dispatch('loadTestimonyByLesson', route.params.id)
    },
    onPublish () {
      this.$store.dispatch('publishTestimony', {
        id: this.getTestimony.id,
        isPublished: true
      })
    },
    onUnpublish () {
      this.$store.dispatch('publishTestimony', {
        id: this.getTestimony.id,
        isPublished: false
      })
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
