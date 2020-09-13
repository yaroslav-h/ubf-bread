<template>
    <div class="header">
        <div class="container">
            <div class="main d-flex align-items-center" :class="{'py-2': !noPadding}">
                <div class="btn-back" v-if="hasBackBtn">
                    <button class="btn btn-link py-1" @click="onBack"><fa icon="chevron-left"/></button>
                </div>
                <router-link v-if="homeLinkTo" class="btn btn-link p-0" :to="homeLinkTo.to">
                    {{homeLinkTo.label}}
                </router-link>
                <div v-if="homeLinkTo" class="mx-1">/</div>
                <div style="width: 100%">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faChevronLeft } from '@fortawesome/free-solid-svg-icons'
library.add(faChevronLeft)

export default {
  name: 'base-page-header',
  props: {
    noPadding: {
      type: Boolean,
      default: false
    },
    hasBackBtn: {
      type: Boolean,
      default: false
    },
    homeLinkTo: {
      type: Object,
      default: null
    }
  },
  methods: {
    onBack () {
      if (window.history.length > 2) {
        this.$router.back()
      } else {
        this.$router.push('/')
      }
    }
  }
}
</script>

<style scoped lang="scss">
    .header {
        background: #f3f3f3;
    }
    .btn-back {
        .btn {
            padding-left: 6px;
        }
    }
    @media (max-width: 767px) {
        .container {
            padding: 0 8px;
        }
        .btn-back {
            .btn {
                padding-left: 0;
            }
        }
    }
</style>
