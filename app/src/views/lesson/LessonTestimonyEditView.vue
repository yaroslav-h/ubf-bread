<template>
  <main-layout>

    <base-page-header :has-back-btn="true" v-if="getLesson">
      <router-link :to="{name: 'LessonTestimonies', params: {id:getLesson.id}}">{{ $t('Testimonies') }}</router-link> / <router-link :to="{name: 'LessonTestimonyMy', params: {id:getLesson.id}}">{{ $t('My') }}</router-link> / {{ getTestimony != null ? $t('Edit') : $t('Add') }}
    </base-page-header>

    <base-page-body :is-loading="isLoading && getLesson == null" :not-found="!isLoading && getLesson == null">

      <div class="card" v-if="getLesson">
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
        <div class="p-3">
          {{ getLesson.content.key_verse }}
        </div>
        <div class="card-body bg-white">

          <div v-if="!getTestimony" class="text-center">
            <div>{{ $t('If you want you can use a quiz mode to compose your testimony step by step') }}</div>
            <div class="small">{{ $t('Use it if you are new to this') }}</div>
            <div class="my-2">
              <router-link :to="{name:'LessonTestimonyMyEditQuiz', params: {id:getLesson.id}}" class="btn btn-primary">{{ $t('Go to a quiz mode') }}</router-link>
            </div>
          </div>

          <form @submit.prevent="onSave">
            <base-form-group :label="$t('Testimony')">
              <testimony-body-editor v-model="form.body" class="border" style="border-radius: 5px"/>
            </base-form-group>
            <base-form-group :label="$t('Prayer')" labelFor="prayer">
              <textarea id="prayer" name="prayer" v-model="form.prayer" class="form-control"></textarea>
            </base-form-group>
            <base-form-group :label="$t('One word')" labelFor="one_word">
              <base-form-input name="one_word" v-model="form.one_word"/>
            </base-form-group>
            <div class="d-flex justify-content-between">
              <div>
                <button @click="showGuidelines = true" class="btn btn-link" type="button"><fa icon="info-circle" class="mr-2"/>{{ $t('Guidelines') }}</button>
              </div>
              <div>
                <button class="btn btn-link" @click="onCancel" type="button">{{ $t('Cancel') }}</button>
                <button class="btn btn-primary" :disabled="isSaving || !canSave" type="submit">
                  {{ getTestimony != null ? $t('Save') : $t('Create') }} <base-fa-spinner v-if="isSaving" class="ml-2"/>
                </button>
              </div>
            </div>
          </form>
        </div>
        <div></div>
      </div>

    </base-page-body>

    <base-dialog :title="$t('Guidelines')" :do-show="showGuidelines" @close="showGuidelines = false">
      <p>Start a new line and type <code>#</code> followed by a <code>space</code> and you will get an H1 headline.</p>
      <p>This feature is called <strong>input rules</strong>. There are some of these shortcuts for the most basic nodes enabled by default. Try <code>#, ##, ###, …</code> for headlines, <code>></code> for blockquotes, <code>- or +</code> for bullet lists, <code>1.</code> for ordered lists. And of course you can add your own input rules.
    </p>
    </base-dialog>

  </main-layout>
</template>

<script>
import moment from 'moment'
import MainLayout from '@/views/layouts/MainLayout'
import BasePageHeader from '@/components/base/page/BasePageHeader'
import BasePageBody from '@/components/base/page/BasePageBody'
import TestimonyBodyEditor from '@/components/testimony/TestimonyBodyEditor'
import BaseFormGroup from '@/components/base/form/BaseFormGroup'
import BaseFormInput from '@/components/base/form/BaseFormInput'
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faInfoCircle } from '@fortawesome/free-solid-svg-icons'
import BaseDialog from '@/components/base/BaseDialog'
import BasePassageLink from '@/components/base/BasePassageLink'
library.add(faInfoCircle)

export default {
  name: 'LessonTestimonyEditView',
  components: { BasePassageLink, BaseDialog, BaseFaSpinner, BaseFormInput, BaseFormGroup, TestimonyBodyEditor, BasePageBody, BasePageHeader, MainLayout },
  data () {
    return {
      id: null,
      showGuidelines: false,
      form: {
        body: '',
        prayer: '',
        one_word: ''
      }
    }
  },
  computed: {
    formattedDate () {
      return date => moment(date).format('dddd, MMMM Do YYYY')
    },
    isLoading () {
      return this.$store.getters.isLoadingLessonById(this.id) && this.$store.getters.isLoadingTestimonyByLesson(this.id)
    },
    isSaving () {
      return this.$store.getters.isSavingTestimony
    },
    getLesson () {
      return this.$store.getters.getLesson(this.id)
    },
    getTestimony () {
      return this.$store.getters.getTestimonyByLesson(this.id)
    },
    canSave () {
      return this.form.body
    }
  },
  watch: {
    getTestimony (data) {
      this.form.body = data.content.body
    }
  },
  methods: {
    onLoad (route) {
      this.id = route.params.id
      this.$store.dispatch('loadLessonById', route.params.id)
      this.$store.dispatch('loadTestimonyByLesson', route.params.id)
    },
    async onSave () {
      const { err } = await this.$store.dispatch('saveTestimony', {
        id: this.getTestimony?.id,
        lesson_id: this.id,
        content_body: this.form.body,
        content_prayer: this.form.prayer,
        content_one_word: this.form.one_word
      })

      if (err == null) {
        this.$router.push({ name: 'LessonTestimonyMy', params: { id: this.id } })
      }
    },
    onCancel () {
      this.$router.push({ name: 'LessonTestimonyMy', params: { id: this.id } })
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.onLoad(to))
  },
  beforeRouteUpdate (to, from, next) {
    next()
    this.$nextTick(() => this.onLoad(to))
  },
  mounted () {
    this.onLoad(this.$route)
  }
}
</script>

<style scoped>
  .card-body.bg-white {
    background: white !important;
    color: #333333;
  }
</style>
