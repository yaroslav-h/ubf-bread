<template>
    <div class="layout">
        <div class="login-page" :class="{'hide-intro': hideIntro}">

            <div class="login-page-intro">
                <div class="text-intro">
                    <div>One</div>
                    <div>
                        <vue-typer class="typer"
                            :text='["feed","language","world","love","life","."]'
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
                <div class="logo-header"><h1>0nefeed.</h1></div>
                <router-view></router-view>
                <p class="copyright text-muted text-center mb-0">© {{ new Date().getFullYear() }} All rights reserved</p>
            </div>
        </div>
    </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faChevronDown } from '@fortawesome/free-solid-svg-icons'

import { VueTyper } from 'vue-typer'
library.add(faChevronDown)

export default {
  name: 'login-layout',
  components: {
    VueTyper
  },
  data () {
    return {
      hideIntro: false,
      details: {}
    }
  },
  methods: {
    onHideIntro () {
      this.hideIntro = true
      document.documentElement.scrollBy({ top: 999, behavior: 'smooth' })
      document.body.scrollBy({ top: 999, behavior: 'smooth' })
    }
  },
  mounted () {
    this.hideIntro = this.$route.name !== 'Login'
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
            min-height: 60vh;
            overflow: auto;
        }
    }
    @media (min-width: 1200px) {
        .login-page-form {
            padding: 15px 50px;
            min-height: 60vh;
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
        .layout {
            position: relative;
            height: inherit;
            margin-top: -50px;
        }
        .login-page {
            position: relative;
            width: 100%;
            height: inherit;
            display: block;
            transition: all 500ms ease-in;
            overflow: auto;
        }
        .login-page-intro {
            position: relative;
            width: 100%;
            height: 100vh;
            transition: all 500ms ease-in;
            top: 0;
        }
        .login-page-form {
            position: relative;
            width: 100%;
            /*height: 100%;*/
            transition: all 500ms ease-in;
            /*top: 100%;*/
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
</style>
