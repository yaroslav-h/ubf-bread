<template>
  <main-layout>

    <base-page-header :has-back-btn="true">
      <div class="d-flex justify-content-between align-items-center">
        <div>My profile</div>
        <div>
          <router-link :to="{name: 'Logout'}" class="btn btn-sm btn-link">Logout</router-link>
        </div>
      </div>
    </base-page-header>

    <base-page-body>
      {{user}}
    </base-page-body>

  </main-layout>
</template>

<script>
import MainLayout from '@/views/layouts/MainLayout'
import BasePageHeader from '@/components/base/page/BasePageHeader'
import BasePageBody from '@/components/base/page/BasePageBody'

export default {
  name: 'ProfileView',
  components: { BasePageBody, BasePageHeader, MainLayout },
  computed: {
    user () {
      return this.$store.getters['site/identity']
    }
  }
}
</script>

<style scoped>

</style>
