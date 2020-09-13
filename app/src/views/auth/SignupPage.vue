<template>
    <form class="mt-5" @submit.prevent="onSubmit">
        <div class="text-center text-muted mb-3">Sign up to get your feed</div>

        <base-form-group label="Name" labelFor="name">
            <base-form-input id="name" v-model="name" :error="errors['name']" />
        </base-form-group>

        <base-form-group label="Username" labelFor="login">
            <base-form-input id="login" autocomplete="off" v-model="username" :error="errors['username']" />
        </base-form-group>

        <base-form-group label="Password" labelFor="password">
            <div class="password-input">
                <base-form-input id="password" autocomplete="new-password" type="password" v-model="password" :error="errors['password']" />
            </div>
        </base-form-group>

        <base-form-actions>
            <base-btn-submit class="btn-submit" label="Sign up" :is-submitting="isSubmitting"/>
        </base-form-actions>

        <div class="mt-3 text-center">
            <p>If you have an account you can <router-link to="/login">log in</router-link>.</p>
        </div>

        <small>By signing up, you agree to the Terms of Service and Privacy Policy, including Cookie Use.</small>
    </form>
</template>

<script>
import BaseFormGroup from '@/components/base/form/BaseFormGroup'
import BaseBtnSubmit from '@/components/base/btn/BaseBtnSubmit'
import BaseFormInput from '@/components/base/form/BaseFormInput'
import BaseFormCheckbox from '@/components/base/form/BaseFormCheckbox'
import BaseFormActions from '@/components/base/form/BaseFormActions'

import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'signup-page',
  components: {
    BaseFormActions,
    BaseFormCheckbox,
    BaseFormInput,
    BaseBtnSubmit,
    BaseFormGroup
  },
  data () {
    return {
      name: '',
      username: '',
      // email: '',
      password: ''
    }
  },
  computed: {
    ...mapGetters('pages/signup', [
      'isSubmitting', 'errors'
    ])
  },
  methods: {
    onSubmit () {
      this.$store.dispatch('pages/signup/submit', {
        name: this.name,
        username: this.username,
        email: null, // this.email,
        password: this.password
      })
    }
  }
}
</script>

<style lang="scss" scoped>
    .btn-submit {
        width: 100%;
    }
</style>
