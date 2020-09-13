<template>
    <div class="text-input">

        <div class="textarea-scroll-height" ref="textHeight" :style="{'font-size': fontSize+'px'}">{{text}}</div>
        <textarea @input="onTextInput" :value="text" ref="text" rows="1" class="form-control form-control-sm" :class="{'is-invalid': error, 'text-center': textCenter}" :style="textareaStyle" :placeholder="placeholder" :disabled="disabled"></textarea>

        <div class="invalid-feedback" v-if="error">{{error}}</div>

        <div class="emoji-picker-backdrop" v-if="showEmojiPicker" @click="onToggleEmoji"></div>
        <div class="emoji-picker" v-if="enableEmoji">
            <div class="emoji-picker-toggle" :class="{'active': showEmojiPicker}" @click="onToggleEmoji">
                <i v-if="!isEmojiPickerLoading" class="fa fa-smile-o"></i>
                <i v-if="isEmojiPickerLoading" class="fa fa-spin fa-circle-o-notch"></i>
            </div>

            <div class="emoji-picker-popup" v-if="showEmojiPicker">
                <!--<picker :native="true" :show-preview="false" :per-line="8" @select="onSelectEmoji"/>-->
            </div>
        </div>

        <!--<div class="autocomplete" v-if="autocompleting.active">
            <i v-if="autocompleting.isLoading" class="fa fa-spin fa-circle-o-notch"></i>
            <div class="list-group" v-if="!autocompleting.isLoading">
                <a class="list-group-item" v-for="item in autocompleting.options" href="#" @click.prevent="onAutocomplete(item)">
                    <div class="text">{{ item.text }}</div>
                    <small v-if="autocompleting.type === 'users'">{{ item.value }}</small>
                </a>
            </div>
        </div>-->

    </div>
</template>

<script>
import { countLines } from '@/helpers/string'
// import { Picker } from 'emoji-mart-vue'

export default {
  name: 'BaseTextarea',
  props: {
    value: {
      required: false,
      default: ''
    },
    placeholder: {
      required: false,
      type: String,
      default: ''
    },
    enableEmoji: {
      required: false,
      type: Boolean,
      default: false
    },
    enableAutocomplete: {
      required: false,
      type: Boolean,
      default: false
    },
    disabled: {
      required: false,
      type: Boolean,
      default: false
    },
    maxHeight: {
      required: false,
      type: Number,
      default: 0
    },
    textCenter: {
      required: false,
      type: Boolean,
      default: false
    },
    textColor: {
      required: false
    },
    hasAutoScale: {
      required: false,
      type: Boolean,
      default: false
    },
    error: {
      default: null
    }
  },
  components: {
    // Picker
  },
  data () {
    return {
      text: '',
      countLines: 0,
      height: null,
      isEmojiPickerLoading: false,
      showEmojiPicker: false,
      emojiInsertPosition: null,

      autocompleting: {
        active: false,
        type: '', // user or hashtag
        startPos: null,
        curPos: null,
        query: '',
        isLoading: false,
        options: []
      }
    }
  },
  computed: {
    textareaStyle () {
      const style = {}

      if (this.height) {
        style.height = this.height + 'px'
      }

      style['font-size'] = this.fontSize + 'px'

      /* if(this.maxHeight > 0) {
                    style['max-height'] = this.maxHeight+'px';
                    style['overflow'] = 'hidden';
                } */

      if (this.textColor !== 'default') {
        style.color = this.textColor
        style['--t-p-color'] = this.textColor
      }

      return style
    },
    fontSize () {
      if (!this.hasAutoScale) {
        return 16
      }

      if (this.text.length === 0) {
        return 16
      }

      /* if(this.textCenter && this.countLines < 5) {
                    return 32;
                } */

      if (this.countLines < 4) {
        if (window.innerWidth <= 600) {
          return Math.floor(window.innerWidth * 32 / 600)
        }

        return 32
      }

      return 16
    }
  },
  watch: {
    value: {
      immediate: true,
      handler (text) {
        this.text = text
      }
    },
    text (text) {
      this.$emit('input', text)
      this.calcHeight(text)
    },
    'autocompleting.active' (state) {
      if (state === false) {
        this.autocompleting.type = ''
        this.autocompleting.startPos = null
        this.autocompleting.curPos = null
        this.autocompleting.query = ''
      }
    },
    'autocompleting.query' () {
      this.autocompleting.isLoading = true
      // TODO: make debounce!
      /* axios.get(`/api/autocomplete/${this.autocompleting.type}`, {params:{q: val}}).then(({data}) => {
                    this.autocompleting.options = data
                    this.autocompleting.isLoading = false
                }).catch(err => {
                    this.autocompleting.options = []
                    this.autocompleting.isLoading = false
                }) */
    },
    hasAutoScale () {
      this.calcHeight(this.text)
    },
    fontSize: {
      immediate: true,
      handler (val) {
        this.$emit('font-size', val)
      }
    }
  },
  methods: {
    onTextInput (e) {
      if (e.value !== null) {
        this.$emit('input', e.target.value)
      }

      if (!this.enableAutocomplete) return

      if (e.data === '@' || e.data === '#') {
        this.autocompleting.active = true
        this.autocompleting.type = (e.data === '@' ? 'users' : 'tags')
        this.autocompleting.startPos = e.target.selectionStart
      }

      if ((e.data === ' ' || e.target.selectionStart - this.autocompleting.curPos < 0) && this.autocompleting.active) {
        this.autocompleting.active = false
      }

      if (this.autocompleting.active) {
        this.autocompleting.curPos = e.target.selectionStart
        this.autocompleting.query = this.text.substring(this.autocompleting.startPos, this.autocompleting.curPos)
      }
    },
    onAutocomplete (item) {
      const before = this.text.substring(0, this.autocompleting.startPos)
      const after = this.text.substring(this.autocompleting.curPos, this.text.length)

      this.autocompleting.active = false

      this.text = (before + item.value + ' ' + after)

      this.$refs.text.focus()
    },
    onToggleEmoji () {
      this.showEmojiPicker = !this.showEmojiPicker

      this.emojiInsertPosition = this.$refs.text.selectionStart || this.text.length
    },
    onSelectEmoji (emoji) {
      const before = this.text.substring(0, this.emojiInsertPosition)
      const after = this.text.substring(this.emojiInsertPosition, this.text.length)

      this.text = (before + emoji.native + after)
      this.emojiInsertPosition += emoji.native.length
    },
    calcHeight (text) {
      this.countLines = countLines(text)

      this.$nextTick(() => {
        this.height = this.$refs.textHeight.scrollHeight || this.fontSize * 1.5

        if (`${this.text}`.length === 0) {
          this.height = this.fontSize * 1.5
        } else if (`${text}`.split('\n').pop() === '') {
          this.height += this.fontSize * 1.5
        }
      })
    },

    setFocus () {
      this.$refs.text.focus()
    }
  },
  mounted () {
    this.text = this.value
    this.calcHeight(this.text)
  }
}
</script>

<style scoped>
    .form-control {
        border-radius: 0;
        border: 1px solid transparent;
        resize: none;
        overflow: hidden;
        outline: 0;
        box-shadow: none;
        padding: 0;
    }

    .textarea-scroll-height {
        /*Don't make display as none. scrollHeight and offsetHeight is 0 for display property as none.*/
        overflow: hidden;
        word-wrap: break-word;
        white-space: pre-wrap;
        width: 100%;
        height: 0;
        padding: 0;
        font-size: 16px;
        line-height: 1.5;
    }
    textarea {
        font-size: 16px;
        line-height: 1.5;
        border: none;
        background-color: transparent !important;
        color: #212529;
        --t-p-color: #555555;
    }
    textarea::placeholder {
        color: var(--t-p-color);
    }

    .emoji-picker-backdrop {
        position: fixed;z-index: 1100;width: 100%; height: 100%; left: 0; top: 0;
    }
    .text-input {
        position: relative;
    }
    .emoji-picker {
        position: absolute;right: 0; top: 6px;
    }
    .emoji-picker-toggle {
        font-size: 18px;line-height: 0;
    }
    .emoji-picker-toggle.active {
        color: blue;
    }
    .emoji-picker-popup {
        position: absolute;z-index:1102;margin-left: -304px;margin-top: 5px;
    }

    .autocomplete {
        position: absolute;
        top: 100%;
        width: 100%;
        background: white;
    }

    .list-group {
        padding: 0;
        margin: 0;
    }

    .list-group-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px;
    }

    @media (max-width: 767px) {
        .emoji-picker {
            display: none;
        }
    }
</style>
