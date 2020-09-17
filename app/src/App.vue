<template>
  <div id="app" class="pb-3" :class="{dark: isDarkMode}">
    <router-view/>

    <base-dialog :as-menu="true" :title="$t('Select a language')" :do-show="$store.getters.getUI.showLangSelector" :show-footer="false"
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
  a.testimony-item {
    background-color: #414c4f !important;
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

$color-black: #000000;
$color-white: #ffffff;
$color-grey: #dddddd;
.editor__content .ProseMirror:focus {
  outline: none;
}
.editor {
  position: relative;
  max-width: 30rem;
  margin: 0 auto 5rem auto;

  &__content {
    padding: .25rem;
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;

    * {
      caret-color: currentColor;
    }

    pre {
      padding: 0.7rem 1rem;
      border-radius: 5px;
      background: $color-black;
      color: $color-white;
      font-size: 0.8rem;
      overflow-x: auto;

      code {
        display: block;
      }
    }

    p code {
      padding: 0.2rem 0.4rem;
      border-radius: 5px;
      font-size: 0.8rem;
      font-weight: bold;
      background: rgba($color-black, 0.1);
      color: rgba($color-black, 0.8);
    }

    ul,
    ol {
      padding-left: 1rem;
    }

    li > p,
    li > ol,
    li > ul {
      margin: 0;
    }

    a {
      color: inherit;
    }

    blockquote {
      border-left: 3px solid rgba($color-black, 0.1);
      color: rgba($color-black, 0.8);
      padding-left: 0.8rem;
      font-style: italic;

      p {
        margin: 0;
      }
    }

    img {
      max-width: 100%;
      border-radius: 3px;
    }

    table {
      border-collapse: collapse;
      table-layout: fixed;
      width: 100%;
      margin: 0;
      overflow: hidden;

      td, th {
        min-width: 1em;
        border: 2px solid $color-grey;
        padding: 3px 5px;
        vertical-align: top;
        box-sizing: border-box;
        position: relative;
        > * {
          margin-bottom: 0;
        }
      }

      th {
        font-weight: bold;
        text-align: left;
      }

      .selectedCell:after {
        z-index: 2;
        position: absolute;
        content: "";
        left: 0; right: 0; top: 0; bottom: 0;
        background: rgba(200, 200, 255, 0.4);
        pointer-events: none;
      }

      .column-resize-handle {
        position: absolute;
        right: -2px; top: 0; bottom: 0;
        width: 4px;
        z-index: 20;
        background-color: #adf;
        pointer-events: none;
      }
    }

    .tableWrapper {
      margin: 1em 0;
      overflow-x: auto;
    }

    .resize-cursor {
      cursor: ew-resize;
      cursor: col-resize;
    }

  }
}
</style>
