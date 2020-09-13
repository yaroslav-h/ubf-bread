<template>
    <div class="btn-dropdown-menu">
        <!--<div class="btn-group-backdrop-header" :class="{'show': isShown}" @click="isShown = false"></div>-->
        <div class="btn-group-backdrop" :class="{'show': isShown}" @click="isShown = false"></div>
        <div class="btn-group" :class="{'show': isShown}">
            <button type="button" class="btn btn-sm btn-link dropdown-toggle ws-nw" @click="onToggle">
              {{label}} <fa v-if="icon" :icon="buttonIcon" :spin="isLoading" />
            </button>
            <div class="dropdown-menu" :class="{'show': isShown, 'dropdown-menu-right': dir === 'right'}">
                <h6 v-if="header" class="dropdown-header">{{ header }}</h6>
                <div v-if="header" class="dropdown-divider"></div>

                <slot></slot>

            </div>
        </div>
    </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEllipsisV, faTimes, faPencilAlt } from '@fortawesome/free-solid-svg-icons'
library.add(faEllipsisV, faTimes, faPencilAlt)

export default {
  name: 'base-dropdown-menu',
  props: {
    header: {
      required: false,
      type: String,
      default: 'Options'
    },
    icon: {
      required: false,
      type: String,
      default: 'ellipsis-v'
    },
    label: {
      required: false,
      type: String,
      default: ''
    },
    dir: {
      required: false,
      type: String,
      default: 'left'
    },
    isLoading: {
      required: false,
      type: Boolean,
      default: false
    },
    mobHidOpeIco: {
      required: false,
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      isShown: false
    }
  },
  computed: {
    buttonIcon () {
      if (this.isLoading) {
        return ['fas', 'circle-notch']
      } else if (this.isShown) {
        return ['fas', 'times']
      }

      return ['fas', this.icon]
    }
  },
  watch: {
    isShown (value) {
      this.$emit('show', value)
    },
    '$route' () {
      this.close()
    }
  },
  methods: {
    onToggle () {
      if (!this.isLoading) {
        this.isShown = !this.isShown
      }
    },
    open () {
      this.isShown = true
    },
    close () {
      this.isShown = false
    }
  }
}
</script>

<style lang="scss" scoped>

    .btn-dropdown-menu {
        display: inherit;
    }

    .btn-group-backdrop.show {
        z-index: 1500;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }
    .btn-group.show .btn {
        z-index: 1005;
    }
    .btn-group.show {
        z-index: 1501;
    }
    /*.dropdown-menu button {
        display: block;
        cursor: pointer;
        padding: .25rem;
        width: 100% !important;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: left !important;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        outline: none;
    }*/

    .dropdown-menu button:disabled {
        cursor: default;
        color: gray;
    }

    .dropdown-menu button:not(:disabled):focus, .dropdown-menu button:not(:disabled):hover {
        color: #16181b;
        text-decoration: none;
        background-color: #f8f9fa;
    }

    /*.dropdown-menu {
        top: -4px;
        left: initial;
        padding: 5px;
    }*/

    .to-left .dropdown-menu {
        right: -4px;
    }
    .to-right .dropdown-menu {
        left: -4px;
    }

    .to-left .btn-toggle {
        /*margin-right: -5px;*/
    }
    .to-right .btn-toggle {
        /*margin-left: -5px;*/
    }

    .to-right .dropdown-header {
        padding-left: 26px;
    }

    .dropdown-header {
        padding: 0.25rem 1.5rem;
    }
    .dropdown-divider  {
        margin: .25rem 0;
    }
</style>
