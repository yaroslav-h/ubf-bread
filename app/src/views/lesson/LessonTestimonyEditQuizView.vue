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

          <div v-if="step !== steps.length">
            <div class="h5">{{ steps[step].h1 || steps[step].h2 || steps[step].h3 }}</div>
            <div>{{ steps[step].ask }}</div>
            <div>
              <textarea class="form-control" :class="{'is-invalid': !canNext}" v-model="steps[step].paragraph"></textarea>
            </div>
          </div>
          <div v-else class="text-center p-3">
            <span v-if="form.body === ''">{{ $t('You have not filled up main blocks') }}</span>
            <span v-else>{{ $t('Well done. Click \'Create\' to compose your testimony') }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <div>{{step+1}}/{{steps.length+1}}</div>
            <div>
              <button class="btn btn-link" @click="onPrev" v-if="step > 0">{{ $t('Previous') }}</button>
              <button v-if="step < steps.length" class="btn btn-primary" @click="onNext" :disabled="!canNext">{{ $t('Next') }}</button>
              <button v-else class="btn btn-primary" @click="onSave" :disabled="isSaving || form.body === ''">{{ $t('Create') }}<base-fa-spinner v-if="isSaving" class="ml-2"/></button>
            </div>
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
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faInfoCircle } from '@fortawesome/free-solid-svg-icons'
import BasePassageLink from '@/components/base/BasePassageLink'
library.add(faInfoCircle)

export default {
  name: 'LessonTestimonyEditQuizView',
  components: { BasePassageLink, BaseFaSpinner, BasePageBody, BasePageHeader, MainLayout },
  data () {
    return {
      id: null,
      step: 0,
      body: '',
      form: {
        body: '',
        prayer: '',
        one_word: ''
      },
      steps: [
        { ask: this.$i18n.t('Шо написано про Бога Батька, Його єдиного Сина Ісуса Христа та про Святого Духа'), h3: this.$i18n.t('Вступ'), paragraph: '' },
        { ask: this.$i18n.t('що потрібно виконувати в християнському житті'), paragraph: '', h3: this.$i18n.t('Частина перша') },
        { ask: this.$i18n.t('чи є обіцянки, на які ми можемо надіятись?'), paragraph: '' },
        { ask: this.$i18n.t('чи є вказівки, яким треба підкорятись?'), paragraph: '' },
        { ask: this.$i18n.t('чи є застереження, на які варто звернути увагу?'), paragraph: '' },
        { ask: this.$i18n.t('чи є у вас звички, які треба змінити?'), paragraph: '' },
        { ask: this.$i18n.t('чи є гріхи, яких треба уникати?'), paragraph: '' },
        { ask: this.$i18n.t('яке вчення є найбільш важливим в цьому уривку?'), h3: this.$i18n.t('найбільш важливе'), paragraph: '' },
        { ask: this.$i18n.t('який вірш є ключовим?'), paragraph: '' },

        { ask: this.$i18n.t('Prayer'), paragraph: '', field: 'prayer' },
        { ask: this.$i18n.t('One word'), paragraph: '', field: 'one_word' }
      ]
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
    canNext () {
      return this.steps[this.step].paragraph || (this.steps[this.step].h1 || this.steps[this.step].h2 || this.steps[this.step].h3) == null
    }
  },
  watch: {
    getTestimony (data) {
      this.form.body = data.content.body
    }
  },
  methods: {
    onPrev () {
      if (this.step > 0) this.step--
      this.buildForm()
    },
    onNext () {
      if (this.step < this.steps.length) this.step++
      this.buildForm()
    },
    buildForm () {
      this.body = ''
      this.steps.forEach(s => {
        if (s?.field) {
          this.form[s.field] = s.paragraph
        } else if (s.paragraph !== '') {
          this.body = this.body +
            (s?.h1 ? `<h1>${s.h1}</h1>` : '') +
            (s?.h2 ? `<h2>${s.h2}</h2>` : '') +
            (s?.h3 ? `<h3>${s.h3}</h3>` : '') +
            `<p>${s.paragraph}</p>`
        }
      })
      this.form.body = this.body
    },
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
