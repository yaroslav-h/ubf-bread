<template>
    <div>
        <div class="modal-backdrop fade" v-if="isShown" :class="{show:isShown}"></div>

        <div class="modal fade" @click.self="close" v-if="isShown" :class="{show:isShown, 'as-menu': asMenu}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{title}}</h5>
                        <button type="button" class="close" @click.prevent="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="hasBody && !asMenu">
                        <slot></slot>
                    </div>
                    <div class="modal-footer btn-list" v-if="asMenu">
                        <slot name="actions"></slot>
                        <button type="button" class="btn btn-link" @click.prevent="close">Cancel</button>
                    </div>
                    <div class="modal-footer" v-else-if="showFooter">
                        <button type="button" class="btn btn-sm btn-default mr-auto" @click.prevent="close">Cancel</button>
                        <slot name="actions"></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: 'BaseDialog',
  props: {
    title: {
      required: true,
      type: String
    },
    hasBody: {
      required: false,
      type: Boolean,
      default: true
    },
    showFooter: {
      required: false,
      type: Boolean,
      default: true
    },
    asMenu: {
      required: false,
      type: Boolean,
      default: false
    },
    doShow: {
      required: false,
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      show_local: false
    }
  },
  computed: {
    isShown () {
      return this.show_local || this.doShow
    }
  },
  watch: {
    isShown (state) {
      if (state) {
        window.document.body.style.overflow = 'hidden'
      } else {
        window.document.body.style.removeProperty('overflow')
      }
    }
  },
  methods: {
    show () {
      this.show_local = true
    },
    close () {
      this.show_local = false
      this.$emit('close')
    }
  },
  mounted () {

  }
}
</script>

<style scoped lang="scss">
    .modal {
        display: block
    }

    .modal-footer.btn-list {
        flex-direction: column;

        button {
            width: 100%;
            margin: 0;
        }
    }

    .modal.as-menu {
        display: flex;
        justify-content: center;
        padding-top: 100px;

        .modal-dialog {
            width: 240px;
        }

        .modal-header {
            padding: 6px 12px;
            .close {
                display: none;
            }
        }
        .modal-footer {
            border-top: none;
            padding: 0.5rem;

        }
        .btn-list > button {
            border-bottom: 1px solid #f1f1f1;
        }
        .btn-list > button:last-child {
            border-bottom: none;
        }
    }
</style>
