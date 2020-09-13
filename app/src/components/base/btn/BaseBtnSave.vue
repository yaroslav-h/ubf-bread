<template>
    <button type="submit" :disabled="isSaving" @click.prevent="$emit('save')" class="btn btn-sm" :class="{'btn-primary': !hasSavedState, 'btn-success': hasSavedState}">
        <fa v-if="isSaving" icon="circle-notch" spin class="mr-2" />
        <fa v-if="hasSavedState" icon="check" class="mr-2"></fa>

        <span v-if="text">{{ text }}</span>
    </button>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCheck, faCircleNotch, faTimes } from '@fortawesome/free-solid-svg-icons'
library.add(faCheck, faCircleNotch, faTimes)

export default {
  name: 'base-btn-save',
  props: {
    title: {
      required: false,
      type: String,
      default: 'Save'
    },
    isSaving: {
      required: true,
      type: Boolean
    },
    isSaved: {
      required: true,
      type: Boolean
    }
  },
  data () {
    return {
      hasSavedState: false
    }
  },
  computed: {
    text () {
      return this.title
    }
  },
  watch: {
    isSaving (state) {
      if (state === true && this._timeout) {
        this.hasSavedState = false
        clearTimeout(this._timeout)
        this._timeout = null
      }
    },
    isSaved (state) {
      if (state === true) {
        this.hasSavedState = true
        this._timeout = setTimeout(() => this.hasSavedState = false, 2000)
      }
    }
  }
}
</script>

<style scoped>
</style>
