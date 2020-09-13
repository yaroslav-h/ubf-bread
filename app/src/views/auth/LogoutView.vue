<template>
    <div class="l">
        <vue-typer class="typer"
                   :text='["bye..."]'
                   :repeat='0'
                   initial-action='typing'
                   :pre-type-delay='70'
                   :type-delay='270'
                   :pre-erase-delay='1500'
                   :erase-delay='50'
                   erase-style='backspace'
                   :erase-on-complete='false'
                   caret-animation='blink'
                   @completed='onLogout'
        ></vue-typer>
    </div>
</template>

<script>
import { VueTyper } from 'vue-typer'

export default {
  name: 'LogoutView',
  components: {
    VueTyper
  },
  methods: {
    onLogout () {
      this.$router.push({ name: 'Home' })
    }
  },
  mounted () {
    this.$store.dispatch('site/logout')
  }
}
</script>

<style lang="scss">
    .l {
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
<style scoped>
.l {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;

    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 46px;
    font-weight:bold;
}
</style>
