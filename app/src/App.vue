<template>
  <div id="app" :class="{dark: isDarkMode}">
    <router-view/>

    <base-dialog :title="$t('select a language')" :do-show="$store.getters.getUI.showLangSelector" :show-footer="false"
                 @close="$store.dispatch('uiCloseLangSelector')">
      <div class="list-group">
        <a @click.prevent="setLocale(locale.code)" href="#" v-for="locale in $store.getters.getUI.locales" :key="locale.code" class="list-group-item" :class="{active: locale.locale === $store.getters.getUILangLocale}">
          <span style="text-transform: uppercase">{{locale.code}}</span> - {{locale.name}}
        </a>
      </div>
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
    border-color: #414c4f !important;
    color: white;
  }
  .vc-title {
    color: white;
  }
}
</style>
