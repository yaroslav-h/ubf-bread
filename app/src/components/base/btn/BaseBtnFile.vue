<template>
    <button class="btn btn-sm btn-primary" :class="{disabled: disabled || isLoading}" type="button">
        <label>
            <input type="file" name="file"
                   :disabled="disabled || isLoading"
                   :multiple="multiple"
                   :accept="accept"
                   @change="onChange" />

            <span v-if="isLoading">
                <fa icon="circle-notch" spin class="mr-sm-2"/><span class="d-none d-sm-inline-block">{{label}}</span>
            </span>
            <span v-else>
                <fa v-if="icon" :icon="icon" class="mr-sm-2"/><span class="d-none d-sm-inline-block">{{label}}</span>
            </span>

        </label>
    </button>
</template>

<script>

import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircleNotch, faImage } from '@fortawesome/free-solid-svg-icons'
library.add(faCircleNotch, faImage)

export default {
  name: 'BaseBtnFile',
  props: {
    accept: {
      type: String,
      default: null
    },
    icon: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    multiple: {
      type: Boolean,
      default: false
    },
    label: {
      type: String,
      default: null
    },
    isLoading: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    onChange (e) {
      if (e.target.files.length > 0) {
        this.$emit('change', e.target.files)
      }
      e.target.value = ''
    }
  }
}
</script>

<style lang="scss" scoped>
    .btn {
        padding: 0;

        label {
            margin: 0;
            cursor: pointer;
            padding: 0.25rem 0.5rem;

            input {
                display:none
            }
        }
    }
</style>
