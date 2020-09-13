<template>
    <div class="body">
        <div class="container">
            <div class="main" ref="main">

                <div v-if="isLoading" class="loader" ><base-fa-spinner/></div>
                <div v-else-if="notFound" class="not-found">
                    <slot name="not-found">
                        Page not found.
                    </slot>
                </div>
                <div v-else>
                    <slot></slot>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import BaseFaSpinner from '@/components/base/BaseFaSpinner'

export default {
  name: 'base-page-body',
  components: { BaseFaSpinner },
  props: {
    notFound: {
      required: false,
      type: Boolean,
      default: false
    },
    isLoading: {
      required: false,
      type: Boolean,
      default: false
    }
  },
  methods: {
    onLoadMore () {
      this.$emit('load-more')
    }
  },
  mounted () {
    this.$emit('width', this.$refs.main.offsetWidth)
  }
}
</script>

<style scoped lang="scss">
    .loader {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100px;
        font-size: 24px;
        color: #dfdfdf;
    }

    .container {
        height: 100%;
        .main {
            height: 100%;
            padding-top: 0;
        }
    }

    @media (max-width: 767px) {
        .container {
            .main {
                padding-top: 0;
            }
        }
    }

    .not-found {
        height: calc(100vh - 124px);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 18px;
    }

</style>
