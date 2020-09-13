<template>

    <div class="layout">
        <div class="login-page" :class="{'hide-intro': hideIntro}">

            <div class="login-page-intro">
                <div class="text-intro">
                    <div>in Jesus we</div>
                    <div>
                        <vue-typer class="typer"
                                   :text='["trust","pray","are one","have victory","have peace"]'
                                   :repeat='Infinity'
                                   :shuffle='false'
                                   initial-action='typing'
                                   :pre-type-delay='70'
                                   :type-delay='70'
                                   :pre-erase-delay='1500'
                                   :erase-delay='50'
                                   erase-style='backspace'
                                   :erase-on-complete='false'
                                   caret-animation='blink'
                        ></vue-typer>
                    </div>
                </div>

                <div class="skip-intro" v-if="!hideIntro" @click="onHideIntro"><fa icon="chevron-down"/></div>
            </div>
            <div class="login-page-form">

                <div>
                    <div class="pt-3 pb-5">
                        <div class="logo-header"><h1>UBFBread</h1></div>
                        <div class="text-center text-muted">{{ $t('Learn Bible') }}</div>

                        <div class="my-5">

                            <div v-if="scenario === 'default'">
                                <form @submit.prevent="onLogin" class="text-left">
                                    <base-form-group :label="$t('username or email')" labelFor="username">
                                        <base-form-input id="username" v-model="section_login.form.username" :error="section_login.errors['username']" />
                                    </base-form-group>
                                    <base-form-group :label="$t('password')" labelFor="password" label-class="w-100">
                                        <template v-slot:label>
                                            <div class="d-flex justify-content-between">
                                                <div>{{ $t('password') }}</div>
                                                <div>
                                                  <!--<router-link :to="{name: 'RecoverPassword'}">Forgot a password?</router-link>-->
                                                </div>
                                            </div>
                                        </template>
                                        <base-form-input id="password" type="password" v-model="section_login.form.password" :error="section_login.errors['password']" />
                                    </base-form-group>
                                    <div class="alert alert-danger" v-if="section_login.errors['error']">
                                        {{section_login.errors['error']}}
                                    </div>

                                    <div class="d-flex w-100 justify-content-center">
                                      <router-link :to="{name: 'Home'}" class="btn btn-link w-100">{{ $t('cancel') }}</router-link>
                                        <button class="btn btn-primary w-100" :disabled="isLoggingIn" @click="onLogin" type="button"><base-fa-spinner v-if="isLoggingIn && section_login.provider == null"/>
                                          {{ $t('login') }}</button>
                                        <!--<button class="btn btn-link w-100" :disabled="isLoggingIn" @click="onShowSignup" type="button">Sign up</button>-->
                                    </div>
                                </form>

                                <div class="mt-3 pt-3 border-top position-relative" v-if="hasGoogleClient">
                                    <div style="position: absolute;top:-9px;left: calc(50% - 10px);font-size: 12px;background: white;padding: 0 5px">OR</div>
                                    <button class="btn btn-sm btn-google" @click="onSocialSignin('google')" :disabled="isLoggingIn" type="button">
                                        <span class="google-icon"></span> Sign in with Google <base-fa-spinner class="ml-2" v-if="isLoggingIn && section_login.provider === 'google'"/>
                                    </button>
                                    <small class="text-danger" v-if="section_login.errors['code']">
                                        {{section_login.errors['code']}}
                                    </small>
                                </div>
                            </div>
                            <div v-if="scenario === 'signup'">

                                <input type="text" id="email" name="email" class="d-none"/>
                                <input type="password" id="password" name="password" class="d-none"/>

                                <form @submit.prevent="onSignup" class="text-left">
                                    <base-form-group label="Email" labelFor="email" v-show="!section_signup.showCode" description="We will send you a code to check your email">
                                        <base-form-input id="email2" type="email" autocomplete="off" v-model="section_signup.form.email" :disabled="true || section_signup.showCode" :error="section_signup.errors['email']"/>
                                    </base-form-group>
                                    <base-form-group label="Password" labelFor="password2" v-show="!section_signup.showCode">
                                        <base-form-input id="password2" autocomplete="new-password" type="password" v-model="section_signup.form.password" :disabled="true || section_signup.showCode" :error="section_signup.errors['password']"/>
                                    </base-form-group>

                                    <div class="alert alert-info" v-if="section_signup.showCode">
                                        <span v-if="isSigningUp && section_signup.resendCode">Sending a new code to your email...</span>
                                        <span v-else>We sent you a short code by email.</span>
                                    </div>

                                    <base-form-group label="Code" labelFor="code" v-show="section_signup.showCode">
                                        <base-form-input id="code" autocomplete="off" type="number" v-model="section_signup.form.code" :error="section_signup.errors['code']" />
                                    </base-form-group>

                                    <div class="alert alert-danger" v-if="section_signup.errors['error']">
                                        {{section_signup.errors['error']}}
                                    </div>

                                    <div class="d-flex w-100 justify-content-center">
                                        <button class="btn btn-link w-25" v-show="!section_signup.showCode" @click.stop="scenario = 'default'" type="button">Cancel</button>
                                        <button class="btn btn-link" style="width: 150px; flex-shrink: 0;" v-show="section_signup.showCode" :disabled="isSigningUp || section_signup.resendTimerLeft > 0" type="button" @click="onResend">
                                            <base-fa-spinner v-if="isSigningUp && section_signup.resendCode"/> Resend<span v-if="section_signup.resendTimerLeft > 0" class="ml-2">({{section_signup.resendTimerLeft}} sec.)</span>
                                        </button>
                                        <button class="btn btn-primary w-100" :disabled="isDisabledSignup" type="button" @click="onSignup">
                                            <base-fa-spinner v-if="isSigningUp && section_signup.provider == null && !section_signup.resendCode"/> Sign up
                                        </button>
                                    </div>
                                </form>

                                <div class="mt-3 pt-3 border-top position-relative" v-if="!section_signup.showCode && hasGoogleClient">
                                    <div style="position: absolute;top:-9px;left: calc(50% - 10px);font-size: 12px;background: white;padding: 0 5px">OR</div>
                                    <button class="btn btn-sm btn-google" @click="onSocialSignup('google')" :disabled="isSigningUp" type="button">
                                        <span class="google-icon"></span> Sign up with Google <base-fa-spinner class="ml-2" v-if="isSigningUp && section_signup.provider === 'google'"/>
                                    </button>
                                    <small class="text-danger" v-if="section_signup.errors['code']">
                                        {{section_signup.errors['code']}}
                                    </small>
                                </div>
                            </div>

                        </div>

                        <!--<div class="mb-4">We use Google <a href="#"><fa icon="question-circle"/></a> as primary identification service.</div>-->

                    </div>

                </div>

                <div class="text-center">
                    <small>
                        By singing up, you agree to the <router-link :to="{name: 'InfoTerms'}">Terms of Service</router-link> and <router-link :to="{name: 'InfoPrivacy'}">Privacy Policy</router-link>, including <router-link :to="{name: 'InfoCookie'}">Cookie Use</router-link>.
                        <span v-if="hasRecaptchaV3">This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy">Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.</span>
                    </small>
                    <p class="copyright text-muted mt-3 mb-0">UBF Kyiv {{ new Date().getFullYear() }}</p>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import BaseFormGroup from '@/components/base/form/BaseFormGroup'
// import BaseBtnSubmit from "@/components/base/btn/BaseBtnSubmit";
import BaseFormInput from '@/components/base/form/BaseFormInput'
// import BaseFormCheckbox from "@/components/base/form/BaseFormCheckbox";
// import BaseFormActions from "@/components/base/form/BaseFormActions";

import BaseFaSpinner from '@/components/base/BaseFaSpinner'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faChevronDown, faQuestionCircle } from '@fortawesome/free-solid-svg-icons'

import { VueTyper } from 'vue-typer'

import { mapApiActions, mapLoadingGetters } from '@/helpers/store'
library.add(faChevronDown, faQuestionCircle)
// import SiteService from "@/services/SiteService";

export default {
  components: {
    BaseFaSpinner,
    // BaseFormActions,
    // BaseFormCheckbox,
    BaseFormInput,
    // BaseBtnSubmit,
    BaseFormGroup,
    VueTyper
  },
  data () {
    return {

      hideIntro: true,

      username: '',
      password: '',

      scenario: 'default',

      section_login: {
        provider: null,
        form: {
          username: '',
          password: ''
        },
        errors: {}
      },
      section_signup: {
        provider: null,
        form: {
          email: '',
          password: '',
          code: ''
        },
        showCode: false,
        resendCode: false,
        resendTimerLeft: 120,
        errors: {}
      },

      userAgent: window.navigator.userAgent,
      hasRecaptchaV3: !!process.env.VUE_APP_RECAPTCHA_V3_PUBLIC_KEY,
      hasGoogleClient: !!process.env.VUE_APP_GOOGLE_CLIENT_ID
    }
  },
  watch: {
    'section_signup.showCode' (state) {
      if (state) {
        this._timer = setInterval(() => {
          this.section_signup.resendTimerLeft--
          if (this.section_signup.resendTimerLeft <= 0) {
            clearInterval(this._timer)
            this._timer = null
          }
        }, 1000)
      } else {
        if (this._timer) {
          clearInterval(this._timer)
          this._timer = null
        }
      }
    }
  },
  beforeDestroy () {
    if (this._timer) {
      clearInterval(this._timer)
      this._timer = null
    }
  },
  computed: {
    /* ...mapGetters('pages/login', [
                'isSubmitting', 'isLoggingIn', 'errors',
            ]), */
    ...mapLoadingGetters('site', {
      isLoggingIn: 'login', isSigningUp: 'signup'
    }),
    isDisabledSignup () {
      return this.isSigningUp || (!this.section_signup.form.username || !this.section_signup.form.email || !this.section_signup.form.password) || (this.section_signup.showCode && !this.section_signup.form.code)
    }
  },
  methods: {
    ...mapApiActions('site', ['login', 'signup']),
    /* onSubmit() {
                this.$store.dispatch('pages/login/submit', {
                    username:   this.username,
                    password:   this.password,
                });
            }, */
    onRefresh () {
      window.location.reload()
    },
    onLogin () {
      if (this.isLoggingIn) {
        return
      }

      this.section_login.provider = null
      this.section_login.errors = {}

      if (this.hasRecaptchaV3) {
        window.grecaptcha.ready(() => {
          window.grecaptcha.execute(process.env.VUE_APP_RECAPTCHA_V3_PUBLIC_KEY, { action: 'login' }).then((token) => {
            this.login({ ...this.section_login.form, token })
              .then(() => {
                this.$router.push('/')
              })
              .catch(error => {
                if (error.validation) {
                  this.section_login.errors = error.validation
                }
              })
          })
        })
      } else {
        this.login({ ...this.section_login.form })
          .then(() => {
            this.$router.push('/')
          })
          .catch(error => {
            if (error.validation) {
              this.section_login.errors = error.validation
            }
          })
      }
    },
    onSocialSignin (provider) {
      if (provider === 'google') {
        this.section_login.provider = 'google'
        this.$googleAuth.getAuthCode()
          .then(code => this.login({ provider, code }))
          .then(() => {
            this.$router.push('/')
          })
          .catch(error => {
            if (error.validation) {
              this.section_login.errors = error.validation
            }
          })
      }
    },
    onSocialSignup (provider) {
      if (provider === 'google') {
        this.section_signup.provider = 'google'
        this.$googleAuth.getAuthCode()
          .then(code => this.signup({ provider, code }))
          .then(() => {
            this.$router.push('/')
          })
          .catch(error => {
            if (error.validation) {
              this.section_signup.errors = error.validation
            }
          })
      }
    },
    onResend () {
      this.section_signup.form.code = ''
      this.section_signup.resendCode = true
      this.onSignup()
    },
    async onSignup () {
      if (this.isSigningUp) {
        return
      }

      this.section_signup.provider = null
      this.section_signup.errors = {}

      if (this.section_signup.form.code || (this.section_signup.showCode && !this.section_signup.resendCode)) {
        this.signup(this.section_signup.form)
          .then(() => {
            this.$router.push('/')
          })
          .catch(error => {
            if (error.validation) {
              this.section_signup.errors = error.validation
            }
          })
      } else {
        window.grecaptcha.ready(() => {
          window.grecaptcha.execute(process.env.VUE_APP_RECAPTCHA_V3_PUBLIC_KEY, { action: 'signup_request' }).then(async (token) => {
            this.signup({
              ...this.section_signup.form,
              code: 'request',
              token
            })
              .then(() => {
                this.section_signup.showCode = true
                this.section_signup.resendCode = false
              })
              .catch(error => {
                this.section_signup.showCode = false
                this.section_signup.resendCode = false

                if (error.validation) {
                  this.section_signup.errors = error.validation
                }
              })
          })
        })
      }

      /* SiteService.signup({username: this.section_signup.form.username, scenario: 'validate'})
                    .then(() => {
                        this.section_signup.isChecking = false;

                        /!*return this.$googleAuth.getAuthCode()
                            .then(code => this.signup({username: this.section_signup.form.username, code}))
                            .catch(error => {
                                if(error.validation) {
                                    this.section_signup.errors = error.validation;
                                }
                            })*!/
                    })
                    .catch(error => {
                        console.log(error.validation);
                        this.section_signup.isChecking = false;

                        if(error.validation) {
                            this.section_signup.errors = error.validation;
                        }
                    }) */
    },

    onShowSignup () {
      this.scenario = 'signup'
      this.section_signup.form.username = ''
      this.section_signup.errors = {}
    },
    onHideIntro () {
      this.hideIntro = true
    },
    onLoad (route) {
      if (route.meta.has_saved_scroll_pos || +route.query.o === 1) {
        this.hideIntro = true
      } else {
        this.hideIntro = false
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.onLoad(to); next()
  },
  beforeRouteEnter (to, from, next) {
    next(vm => vm.onLoad(to))
  }
}
</script>

<style lang="scss">
    .text-intro {
        .typer {
            .custom.char {
                color: #333;
                background-color: white;
            }
            .custom.char.selected {
                background-color: white;
            }
            .custom.caret {
                width: 5px;
                background-color: #333;
            }
        }
    }
</style>
<style lang="scss" scoped>
    .password-input {
        position: relative;

        .reset-link {
            position: absolute;
            top: 0;
            right: 0;
            display: flex;
            height: 100%;
            padding: 0 5px 0 0;
            align-items: center;
        }
    }

    .btn-submit {
        width: 100%;
    }

    .layout {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .login-page {
        display: flex;
        width: 100vw;
        height: 100vh;
        box-shadow: 0 0 8px #7c7c7c;
    }

    .login-page-intro {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 75%;
        color: white;
        box-shadow: 0 0 7px #242424;
        background: linear-gradient(60deg, #3da878, #39a7d0, #c61136);
        background-size: 600% 600%;
        animation: AnimationName 15s ease infinite;
        padding: 15px;
    }

    @keyframes AnimationName {
        0%{background-position:76% 0%}
        50%{background-position:25% 100%}
        100%{background-position:76% 0%}
    }

    @keyframes Gradient {
        0% {
            background-position: 0% 50%
        }
        50% {
            background-position: 100% 50%
        }
        100% {
            background-position: 0% 50%
        }
    }

    .login-page-form {
        width: 40%;
        padding: 15px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

    }

    .logo-header {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .text-intro {
        display: flex;
        color: white;
        text-transform: uppercase;
        font-size: 46px;
        font-weight:bold;
      flex-direction: column;

        > div:first-child {
            margin-right: 5px;
        }
    }

    .skip-intro {
        display: none;
        position: absolute;
        top: calc(100% - 80px);
        left: 50%;
        width: 50px;
        height: 50px;

        font-size: 20px;
        border-radius: 100%;
        color: white;
        text-align: center;
        justify-content: center;
        align-items: center;
        margin: -70px 0 0 -25px;

        animation-name: bounce;
        transform-origin: center bottom;
        animation-iteration-count: infinite;
        animation-duration: 1.5s;
        animation-fill-mode: both;
    }

    @keyframes bounce {
        0%, 40%, 60%, 70%, 100% {
            transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
            transform: translate3d(0,0,0);
        }
        55% {
            transition-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
            transform: translate3d(0, -6px, 0);
        }
        65% {
            transition-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
            transform: translate3d(0, -3px, 0);
        }
    }

    @media (max-width: 1024px) {
        .login-page-form {
            width: 50%;
            height: 100vh;
            overflow: auto;
        }
    }
    @media (min-width: 1200px) {
        .login-page-form {
            padding: 15px 50px;
            height: 100vh;
            overflow: auto;
        }
    }
    @media (min-width: 1500px) {
        .login-page-form {
            padding: 15px 100px;
        }
    }
    @media (min-width: 1800px) {
        .login-page-form {
            padding: 15px 150px;
        }
    }

    @media (max-width: 767px) {

        .login-page {
            display: block;
        }
        .login-page.hide-intro {
            .login-page-intro {
                top: -100vh;
            }
        }
        .login-page-intro {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 94vh;
            transition: all 500ms ease-in;
            z-index: 1;
        }
        .login-page-form {
            width: 100%;
        }

        .login-page.hide-intro .login-page-intro {
            /*margin-top: -100%;*/
        }
        .login-page.hide-intro .login-page-form {
            /*top: 0;
            height: 100vh;*/
        }

        .skip-intro {
            display: flex;
        }

        .text-intro {
            font-size: 36px;
        }
    }

    .google-icon {
        background: url("/img/icons/btn_google_signin_light_normal_web.png") no-repeat;
        width: 30px;
        height: 30px;
        display: inline-block;
        background-position: -8px -8px;
    }
    .btn-google {
        display: flex;
        justify-content: center;
        align-items: center;
        background: white;
        width: 100%;
        box-shadow: 0 1px 3px #7a7a7a;
    }
</style>
