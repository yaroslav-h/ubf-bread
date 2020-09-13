<template>
  <main-layout>
    {{user}}
  </main-layout>
</template>

<script>
import MainLayout from '@/views/layouts/MainLayout'

export default {
  name: 'ProfileView',
  components: { MainLayout },
  computed: {
    user () {
      return this.$store.getters['site/identity']
    }
  }
}
</script>

<style scoped>

</style>
