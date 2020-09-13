<template>
    <button class="btn btn-sm btn-link" @click="onBack">
        <slot>
            Back
        </slot>
    </button>
</template>

<script>
export default {
  name: 'BaseBtnBack',
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

<style scoped>

</style>
