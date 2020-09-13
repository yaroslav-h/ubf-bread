<template>
    <button class="btn btn-primary" type="submit" :disabled="isSubmitting">
        {{ label }} <base-fa-spinner v-if="isSubmitting"/>
    </button>
</template>

<script>
import BaseFaSpinner from '@/components/base/BaseFaSpinner'

export default {
  name: 'base-btn-submit',
  components: {
    BaseFaSpinner
  },
  props: {
    label: {
      required: false,
      type: String,
      default: ''
    },
    isSubmitting: {
      required: false,
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>

</style>
