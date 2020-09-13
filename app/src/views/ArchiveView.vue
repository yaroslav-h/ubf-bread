<template>
  <main-layout>

    <base-page-header v-if="showChapterView" :has-back-btn="true">
      <div class="d-flex align-items-center">
        <div class="w-100">{{ showChapterName }}</div>
        <div class="btn-group">
          <button type="button" @click="showDropdown = !showDropdown" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ year || 'All' }} ({{getLessonsBySelectedChapterByYear.length}})
          </button>
          <div class="dropdown-menu dropdown-menu-right" :class="{show: showDropdown}">
            <router-link :to="{name:'ArchiveChapter', params: {chapter: showChapterName}, query: {year:yyyy}}" v-for="yyyy in [...getYearsBySelectedChapter].reverse()" :key="yyyy" class="dropdown-item">{{ yyyy }}</router-link>
          </div>
        </div>
      </div>
    </base-page-header>

    <base-page-body :is-loading="isLoading || isLoadingLessonsBySelectedChapter">

      <div v-if="showChapterView" class="mt-2">
        <div class="list-group">
          <div class="list-group-item d-flex align-items-center" v-for="lesson in getLessonsBySelectedChapterByYear" :key="lesson.id">
            <div class="mr-3 text-center">
              <div>{{formatLessonDay(lesson)}}</div>
              <div class="ws-nw">{{formatLessonMonYear(lesson)}}</div>
            </div>
            <div class="w-100">
              <div>
                <div class="h6"><fa v-if="lesson.is_intro" icon="play-circle" class="mr-2"/>{{ lesson.title }}</div>
                <div><a :href="lesson.passage_link" target="_blank"><fa icon="book-open" class="mr-2"/>{{lesson.passage}}</a></div>
              </div>
            </div>
            <div>
              <div class="d-flex ws-nw">
                <div v-if="lesson.is_passed"><fa icon="check-circle"/> Passed</div>
                <div v-else-if="lesson.is_read"><fa icon="check"/> Read</div>

                <div class="ml-2"><fa icon="eye"/> {{lesson.user_reads_count}}</div>
                <div class="ml-2"><fa icon="paper-plane"/> {{lesson.testimonies_count}}</div>
              </div>
            </div>
            <div class="ml-2">
              <router-link class="btn btn-link" :to="{name: 'Lesson', params: {id:lesson.id}}"><fa icon="eye"/></router-link>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <div class="card mt-2" v-if="!isLoading">
          <div class="card-header">Years</div>
          <div class="card-body">
            <ul class="nav nav-pills nav-years">
              <li class="nav-item">
                <a class="nav-link" @click.prevent="year = 0" :class="{active: year === 0}" href="#">All ({{years.reduce((acc, val) => acc+=+val.total, 0) }})</a>
              </li>
              <li class="nav-item" v-for="item in years" :key="item.year">
                <a class="nav-link" @click.prevent="year = item.year" :class="{active: year === item.year}" href="#">{{ item.year }} ({{ item.total }})</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="card mt-2" v-if="!isLoading">
          <div class="card-header">Chapters</div>
          <div class="card-body">
            <ul v-if="!isLoadingsChaptersForYear[year]" class="nav nav-pills nav-chapters mt-2">
              <li class="nav-item" v-for="item in getChapters" :key="item.chapter">
                <router-link :to="{name: 'ArchiveChapter', params: {chapter: item.chapter}, query: year ? {year} : {}}" class="nav-link">
                  {{ item.chapter }} ({{ item.total }})
                </router-link>
              </li>
            </ul>
            <div v-else class="text-center p-3">
              <base-fa-spinner/> loading chapters for {{ year }} year...
            </div>
          </div>
        </div>
      </div>

    </base-page-body>

  </main-layout>
</template>

<script>
import MainLayout from '@/views/layouts/MainLayout'
import BasePageBody from '@/components/base/page/BasePageBody'
import LessonsService from '@/services/LessonsService'
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
import BasePageHeader from '@/components/base/page/BasePageHeader'
import moment from 'moment'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCheck, faCheckCircle, faQuestion } from '@fortawesome/free-solid-svg-icons'
library.add(faCheck, faCheckCircle, faQuestion)

export default {
  name: 'ArchiveView',
  components: { BasePageHeader, BaseFaSpinner, BasePageBody, MainLayout },
  data () {
    return {
      isLoading: false,
      year: 0,
      years: [],
      chapters: {},
      isLoadingsChaptersForYear: {},
      showChapterView: false,
      showChapterName: '',
      byChapter: {},
      showDropdown: false
    }
  },
  watch: {
    year (year) {
      this.onLoadYear(year)
    }
  },
  computed: {
    getChapters () {
      return (this.chapters[this.year] || []).filter(a => a.chapter).sort((a, b) => {
        return a.chapter.localeCompare(b.chapter)
      })
    },

    isLoadingLessonsBySelectedChapter () {
      return this.$store.getters.isLoadingLessonsByChapter(this.showChapterName)
    },
    getLessonsBySelectedChapter () {
      return this.$store.getters.getLessonsByChapter(this.showChapterName)
    },
    getLessonsBySelectedChapterByYear () {
      return this.year ? this.getLessonsBySelectedChapter.filter(l => {
        return +(l.date.split('-')[0]) === +this.year
      }) : this.getLessonsBySelectedChapter
    },
    getYearsBySelectedChapter () {
      const years = this.getLessonsBySelectedChapter.map(l => l.date.split('-')[0])
      return years.filter((y, i) => {
        return years.indexOf(y) === i
      })
    }
  },
  methods: {

    formatLessonDay ({ date }) {
      return moment(date).format('DD')
    },
    formatLessonMonYear ({ date }) {
      return moment(date).format('MMM yyyy')
    },

    async onLoadYear (year) {
      if (this.chapters[year] && this.chapters[year].length) return
      if (this.isLoadingsChaptersForYear[year]) return
      if (this.$route.name !== 'Archive') return

      this.$set(this.isLoadingsChaptersForYear, year, true)
      this.$set(this.chapters, year, [])
      try {
        const { data } = await LessonsService.getChapters(year)
        this.chapters[year] = data
      } catch (err) {}
      this.$set(this.isLoadingsChaptersForYear, year, false)
    },
    async onLoad () {
      this.showChapterView = false
      if ((this.years.length && this.chapters[0]) || this.isLoading) {
        return null
      }
      this.isLoading = true
      this.$set(this.chapters, 0, [])
      try {
        const [years, chapters] = await Promise.all([LessonsService.getYears(), LessonsService.getChapters()])
        this.years = years.data
        this.chapters[0] = chapters.data
      } catch (err) {}
      this.isLoading = false
    },
    update () {
      this.showDropdown = false
      if (this.$route.name === 'Archive') {
        this.onLoad()
      } else if (this.$route.name === 'ArchiveChapter') {
        console.log(this.$route)
        this.showChapterView = true
        this.showChapterName = this.$route.params.chapter
        this.year = this.$route.query.year
        if (this.getLessonsBySelectedChapter.length === 0) {
          this.$store.dispatch('loadLessonsByChapter', this.showChapterName)
        }
      }
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.update())
  },
  beforeRouteUpdate (to, from, next) {
    next()
    this.$nextTick(() => this.update())
  },
  mounted () {
    this.update()
  }
}
</script>

<style lang="scss" scoped>
.nav-years {
  .nav-item {
    width: 20%;
    text-align: center;
    .nav-link {
      padding: 0.2rem;
    }
  }
}
.nav-chapters {
  .nav-item {
    width: calc(100% / 3);
    text-align: center;
    .nav-link {
      padding: 0.2rem;
    }
  }
}

@media (max-width: 767px) {
  .nav-years {
    .nav-item {
      width: calc(100% / 3);
    }
  }
  .nav-chapters {
    .nav-item {
      width: 100%;
    }
  }
}
</style>
