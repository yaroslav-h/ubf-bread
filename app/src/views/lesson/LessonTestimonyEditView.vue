<template>
  <main-layout>

    <base-page-header :has-back-btn="true" v-if="getLesson">
      {{getLesson.title}}
      <!--{{ formattedDate(getLesson.date) }}-->
    </base-page-header>

    <base-page-body :is-loading="isLoading && getLesson == null" :not-found="!isLoading && getLesson == null">

      <div class="bg-white p-2">
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
              <button class="btn btn-link" type="button">{{ $t('Cancel') }}</button>
              <button class="btn btn-primary" :disabled="isSaving" type="submit">
                {{ getTestimony != null ? $t('Save') : $t('Create') }} <base-fa-spinner v-if="isSaving" class="ml-2"/>
              </button>
            </div>
          </div>
        </form>
      </div>

    </base-page-body>

    <base-dialog :title="$t('Guidelines')" :do-show="showGuidelines" @close="showGuidelines = false">
      <p>Start a new line and type <code>#</code> followed by a <code>space</code> and you will get an H1 headline.
      </p><p>This feature is called <strong>input rules</strong>. There are some of these shortcuts for the most basic nodes enabled by default. Try <code>#, ##, ###, …</code> for headlines, <code>></code> for blockquotes, <code>- or +</code> for bullet lists. And of course you can add your own input rules.
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
library.add(faInfoCircle)

export default {
  name: 'LessonTestimonyEditView',
  components: { BaseDialog, BaseFaSpinner, BaseFormInput, BaseFormGroup, TestimonyBodyEditor, BasePageBody, BasePageHeader, MainLayout },
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
    onSave () {
      this.$store.dispatch('saveTestimony', {
        id: this.getTestimony?.id,
        lesson_id: this.id,
        content_body: this.form.body,
        content_prayer: this.form.prayer,
        content_one_word: this.form.one_word
      })
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

</style>
