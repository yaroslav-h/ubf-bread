<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container">
                <router-link class="navbar-brand mr-2" to="/" exact>Onefeed.</router-link>

                <div class="uncollapse">
                    <ul class="navbar-nav" style="margin-left: auto">
                        <router-link v-if="identity" tag="li" :to="{ name: 'UserView', params: {username:identity.username} }" class="nav-item nav-profile-item mr-1" active-class="active">
                            <a class="nav-link" style="width: 30px; height: 30px; padding: 0">
                                <user-photo :user="identity"/>
                            </a>
                        </router-link>
                        <router-link v-else tag="li" :to="{ name: 'Login' }" class="nav-item nav-profile-item ml-2">
                            <a class="nav-link">
                                Login<fa class="ml-2" icon="sign-in-alt"/>
                            </a>
                        </router-link>
                    </ul>
                </div>

                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav mr-auto">

                    </ul>
                    <ul class="navbar-nav my-2 my-md-0" v-if="identity">
                        <router-link tag="li" :to="{ name: 'UserView', params: {username:identity.username} }" class="nav-item nav-profile-item ml-2" active-class="active">
                            <a class="nav-link" style="width: 40px; height: 40px; padding: 0">
                                <user-photo :user="identity"/>
                            </a>
                        </router-link>
                    </ul>
                    <ul class="navbar-nav my-2 my-md-0" v-else>
                        <router-link tag="li" :to="{ name: 'Login' }" class="nav-item nav-profile-item ml-2">
                            <a class="nav-link">
                                Login<fa class="ml-2" icon="sign-in-alt"/>
                            </a>
                        </router-link>
                    </ul>
                </div>
            </div>
        </nav>

        <base-page-header>
            Reset Your Password
        </base-page-header>

        <base-page-form :is-loading="isLoading">

            <template v-slot:body>

                <div v-if="form.step === 'email'">
                    <form @submit.prevent="onSubmit" class="mt-2">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3 mb-2">
                                <div class="h5">1/3. Enter your email</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-3 text-right col-form-label">Email</label>
                            <div class="col-sm-6">
                                <input v-model="form.email" :disabled="identity != null" :class="{'is-invalid': errors['email'] != null }" type="email" class="form-control" id="email"/>
                                <div v-if="errors['email'] != null" class="invalid-feedback">{{ errors['email'] }}</div>
                                <small class="form-text text-muted">
                                    We will send you a code on email
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
                <div v-else-if="form.step === 'code'">
                    <form @submit.prevent="onSubmit" class="mt-2">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3 mb-2">
                                <div class="h5">2/3. Enter a code from email</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="code" class="col-sm-3 text-right col-form-label">Code</label>
                            <div class="col-sm-6">
                                <input v-model="form.code" :class="{'is-invalid': errors['code'] != null }" type="number" class="form-control" id="code"/>
                                <div v-if="errors['code'] != null" class="invalid-feedback">{{ errors['code'] }}</div>
                                <small class="form-text text-muted">
                                    The code sent to {{ maskEmail(form.email) }} and it is valid only 15 minutes.
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
                <div v-else-if="form.step === 'password'">
                    <form @submit.prevent="onSubmit" class="mt-2">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3 mb-2">
                                <div class="h5">3/3. Reset a password</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-sm-3 text-right col-form-label">New password</label>
                            <div class="col-sm-6">
                                <input v-model="form.password" :class="{'is-invalid': errors['password'] != null }" type="password" class="form-control" id="password"/>
                                <div v-if="errors['password'] != null" class="invalid-feedback">{{ errors['password'] }}</div>
                                <small class="form-text text-muted">
                                    Your password must be 6-20 characters long, contain letters and numbers, and must not contain spaces or emoji.
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
                <div v-else-if="form.step === 'done'">
                    <div class="alert alert-success my-3">Done! Your password has been changed.</div>
                </div>

            </template>
            <template v-slot:footer>
                <div class="h-100 d-flex justify-content-center align-items-center">
                    <base-btn-back v-if="form.step !== 'done'">Cancel</base-btn-back>
                    <button v-if="form.step !== 'done'" class="btn btn-sm btn-primary" :disabled="isSubmitting" @click="onSubmit">
                        <base-fa-spinner class="mr-2" v-if="isSubmitting"/>Next
                    </button>

                    <router-link to="/" v-if="form.step === 'done'">
                        Return to main
                    </router-link>
                </div>
            </template>

        </base-page-form>
    </div>
</template>

<script>
import UserPhoto from '@/components/user/UserPhoto'
import BasePageForm from '@/components/base/page/BasePageForm'
import BaseBtnBack from '@/components/base/btn/BaseBtnBack'
import BasePageHeader from '@/components/base/page/BasePageHeader'

import { mapGetters } from 'vuex'
import { mapApiActions, mapLoadingGetters } from '@/helpers/store'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faSignInAlt } from '@fortawesome/free-solid-svg-icons'
import SiteService from '@/services/SiteService'
import BaseFaSpinner from '@/components/base/BaseFaSpinner'
library.add(faSignInAlt)

export default {
  name: 'RecoverPasswordPage',
  components: { BaseFaSpinner, BasePageHeader, BaseBtnBack, BasePageForm, UserPhoto },
  data () {
    return {
      isSubmitting: false,
      form: {
        step: 'email',
        email: '',
        code: '',
        password: ''
      },
      errors: {}
    }
  },
  computed: {
    ...mapGetters('site', ['identity']),
    ...mapGetters('account', ['profile']),
    ...mapLoadingGetters('account', { isLoading: 'loadProfile' })
  },
  watch: {
    profile: {
      immediate: true,
      handler (profile) {
        if (profile) {
          this.form.email = this.maskEmail(profile.email)
        }
      }
    }
  },
  methods: {
    ...mapApiActions('account', ['loadProfile']),
    async onSubmit () {
      if (this.isSubmitting === true) {
        return false
      }
      this.isSubmitting = true

      window.grecaptcha.ready(() => {
        window.grecaptcha.execute(process.env.VUE_APP_RECAPTCHA_V3_PUBLIC_KEY, { action: 'recover' }).then(async (token) => {
          const { data, err } = await SiteService.recoverPassword({ ...this.form, token })

          if (err) {
            this.errors = err.validation || {}
          } else {
            this.form.step = data.step
          }

          this.isSubmitting = false
        })
      })
    },
    maskEmail (email) {
      if (typeof email === 'string') {
        const items = email.split('@')

        if (items[0].length >= 2) {
          return items[0].substr(0, 1) + items[0].substr(1, items[0].length - 2).replace(/./ug, '*') + items[0].substr(-1) + '@' + items[1]
        } else {
          return items[0].substr(0, 1) + '@' + items[1]
        }
      }

      return null
    },
    onLoad () {
      if (this.identity) {
        this.loadProfile()
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.onLoad()
    next()
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.onLoad())
  }
}
</script>

<style scoped>
    .uncollapse {
        display: none;
    }
    @media (max-width: 767px) {
        .navbar {
            padding: 4px 0;
        }
        .navbar .container {
            padding: 0 4px 0 8px;
        }
        .navbar-brand {
            margin-right: 12px;
        }

        .uncollapse {
            display: flex;
            flex-basis: auto;
            flex-grow: 1;
            align-items: center;
        }
        .uncollapse .navbar-nav {
            flex-direction: row;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
        }
        .uncollapse .navbar-nav .nav-link {
            padding-right: .5rem;
            padding-left: .5rem;
        }
    }
</style>
