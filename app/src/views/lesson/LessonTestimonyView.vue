<template>
  <main-layout>

    <base-page-header :has-back-btn="true" v-if="getLesson">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div>{{getLesson.title}}</div>
          <div class="small">{{formattedDate(getLesson.date)}}</div>
        </div>
        <div>
          <base-passage-link :src="getLesson.passage" :link="getLesson.passage_link"/>
        </div>
      </div>
    </base-page-header>

    <base-page-body :is-loading="isLoading || getLesson == null || getTestimony == null" :not-found="!isLoading && getLesson == null && getTestimony == null">

      <div class="card mt-2" v-if="getLesson && getTestimony">
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
            </div>
            <div>
              <router-link :to="{name: 'LessonTestimonyEditView', params: {id: getLesson.id}}" class="btn btn-default">
                {{ $t('Edit') }}
              </router-link>
              <button class="btn btn-primary">
                {{ $t('Publish') }}
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
library.add(faUser, faClock)

export default {
  name: 'LessonTestimonyView',
  components: { BasePassageLink, BasePageBody, BasePageHeader, MainLayout },
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
      return date => moment(date).format('DD MMMM YYYY')
    },
    formattedCreatedAt () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    isLoading () {
      return this.$store.getters.isLoadingLessonById(this.id) || this.$store.getters.isLoadingTestimonyByLesson(this.id)
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
