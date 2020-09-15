<template>
  <div id="app" class="pb-3" :class="{dark: isDarkMode}">
    <router-view/>

    <base-dialog :as-menu="true" :title="$t('select a language')" :do-show="$store.getters.getUI.showLangSelector" :show-footer="false"
                 @close="$store.dispatch('uiCloseLangSelector')">
      <template v-slot:actions="">
        <a @click.prevent="setLocale(locale.code)" href="#" v-for="locale in $store.getters.getUI.locales" :key="locale.code" class="btn btn-link" :class="{disabled: locale.locale === $store.getters.getUILangLocale}">
          <span style="text-transform: uppercase">{{locale.code}}</span> - {{locale.name}}
        </a>
      </template>
    </base-dialog>

  </div>
</template>

<script>
import BaseDialog from '@/components/base/BaseDialog'
export default {
  name: 'App',
  components: { BaseDialog },
  methods: {
    setLocale (code) {
      this.$store.dispatch('setUILangCode', code)
      this.$root.$i18n.locale = this.$store.getters.getUILangCode
      this.$store.dispatch('uiCloseLangSelector')
    }
  },
  computed: {
    isDarkMode () {
      return this.$store.getters.getUIIsDarkMode
    }
  },
  watch: {
    isDarkMode: {
      immediate: true,
      handler (val) {
        if (val) {
          document.body.classList.add('dark')
        } else {
          document.body.classList.remove('dark')
        }
      }
    }
  }
}
</script>

<style lang="scss">
@import "node_modules/bootstrap/scss/bootstrap.scss";
.ws-nw {
  white-space: nowrap;
}
body {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  overflow-y: scroll;
  padding-top: 56px;
  min-width: 300px;
}
.container {
  max-width: 720px;
  padding: 0;
}
@media (max-width: 767px) {
  body {
    padding-top: 0px !important;
  }
  .col-sm-12 {
    padding: 0 8px;
  }
  .col-full {
    padding: 0;
  }
  .list-group-item:first-child {
    border-top: 0;
  }
  .list-group-item:last-child {
    border-bottom: 0;
  }
  .list-group-item {
    border-radius: 0 !important;
    border-left: 0;
    border-right: 0;
  }
  .col-sm-12.mt-4 {
    margin-top: .25rem !important;
  }

  .card {
    border-radius: 0;
  }
  .card.mt-2 {
    margin-top: 0 !important;
  }
  .vc-container {
    border: none;
  }
}
.vc-container {
  border: none;
}
body.dark {
  background: #414c4f;
}
#app.dark {
  .header {
    background: #414c4f !important;
    color: white;
  }
  .card {
    background: #373d3e !important;
    color: white;
    .card-header {
      background: #2f3434 !important;
      color: white;
    }
  }
  .modal-content {
    background-color: #373d3e !important;
    color: white;
  }

  .vc-container {
    background: #414c4f !important;
    color: white;
    border: none;
  }
  .vc-title {
    color: white;
  }
  .btn-default {
    color: #b0b0b0;
  }
  .list-group {
    background-color: #414c4f !important;
    div.list-group-item {
      background-color: #373d3e !important;
      color: #e9e9e9;
    }
  }
  a.lesson-item {
    background-color: #414c4f !important;
    .date, .opts {
      color: white;
    }
  }
  .main-view a {
    color: #aed5ff;
  }
  .main-view a:hover {
    color: #74b3ff;
  }
  .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
    background-color: #215598;
  }
  .menu-bottom {
    background: #343a40 !important;
    border-color: #393f45 !important;
  }
}
</style>
