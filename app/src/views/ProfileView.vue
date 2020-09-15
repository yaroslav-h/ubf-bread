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
      <div class="card mt-2">
        <div class="card-body">
          <div class="d-flex justify-content-center">
            <div class="font-weight-bold">Name</div>
            <div class="ml-2">{{user.name}}</div>
          </div>
          <div class="d-flex justify-content-center">
            <div class="font-weight-bold">Email</div>
            <div class="ml-2">{{user.email}}</div>
          </div>
          <div class="d-flex justify-content-center">
            <div class="font-weight-bold">Group</div>
            <div class="ml-2">{{user.group}}</div>
          </div>
          <div class="d-flex justify-content-center">
            <div class="font-weight-bold">Member since</div>
            <div class="ml-2">{{user.created_at}}</div>
          </div>
        </div>
      </div>
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
