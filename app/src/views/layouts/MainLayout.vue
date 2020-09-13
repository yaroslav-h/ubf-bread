<template>
  <div :class="{'show-navbar': showNavbar, 'float-navbar': floatNavbar, 't': transitionNavbar, 'so200': scrollTop200}">
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <div class="container">
        <router-link class="navbar-brand mr-2" to="/" exact v-if="$route.name !== 'Home'"><fa icon="book-open" class="mr-2"/>UBFBread</router-link>
        <a class="navbar-brand mr-2" href="/" @click.prevent="doScrollTop" v-else><fa icon="book-open" class="mr-2"/>UBFBread</a>

        <div class="uncollapse">
          <ul class="navbar-nav" style="margin-left: auto">
            <router-link v-if="isLoggedIn" :to="{ name: 'Profile'}" class="profile">
              <user-avatar :user="user" size="30"/>
            </router-link>
            <router-link v-else :to="{ name: 'Login' }" class="login">
              <fa icon="sign-in-alt"/>
            </router-link>
          </ul>
        </div>

        <div class="collapse navbar-collapse">
          <ul class="navbar-nav mr-auto">
            <!--<router-link tag="li" :to="{ name: 'PostCreate' }" class="nav-item" active-class="active">
                <a class="nav-link"><fa icon="plus"/></a>
            </router-link>-->
          </ul>
          <ul class="navbar-nav my-2 my-md-0">
            <router-link tag="li" :to="{ name: 'Calendar' }" class="nav-item" exact-active-class="active">
              <a class="nav-link"><fa icon="calendar"/> Calendar</a>
            </router-link>
            <router-link tag="li" :to="{ name: 'Archive' }" class="nav-item" active-class="active">
              <a class="nav-link"><fa icon="archive"/> Archive</a>
            </router-link>
            <li class="nav-item">
              <a class="nav-link"><fa icon="globe"/> UA</a>
            </li>
            <router-link v-if="isLoggedIn" tag="li" :to="{ name: 'Profile' }" class="nav-item nav-profile-item ml-2"
                         active-class="active">
              <a class="nav-link" style="width: 40px; height: 40px; padding: 0">
                <user-avatar :user="user" size="36"/>
              </a>
            </router-link>
            <router-link v-else tag="li" :to="{ name: 'Login' }" class="nav-item" exact-active-class="active">
              <a class="nav-link"><fa icon="sign-in-alt"/></a>
            </router-link>
          </ul>
        </div>
      </div>
    </nav>

    <div class="main-view">
      <slot></slot>
    </div>

    <menu-bottom-block v-if="showMobileMenu" class="menu-bottom"/>
  </div>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar'
import MenuBottomBlock from '@/views/layouts/MenuBottomBlock'
import { scrollToTop } from '@/helpers/window'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faPlus,
  faCalendar,
  faSearch,
  faCog,
  faSignOutAlt,
  faBell,
  faTimes,
  faEllipsisV,
  faBars,
  faUser,
  faBookOpen,
  faArchive,
  faGlobe
} from '@fortawesome/free-solid-svg-icons'

library.add(faGlobe, faArchive, faBookOpen, faPlus, faCalendar, faSearch, faCog, faSignOutAlt, faBell, faTimes, faEllipsisV, faBars, faUser)

// import UserPhoto from '@/components/user/UserPhoto'

export default {
  name: 'main-layout',
  components: {
    UserAvatar,
    // UserPhoto,
    MenuBottomBlock
  },
  data () {
    return { showSidebar: false, showNavbar: false, floatNavbar: false, transitionNavbar: false, scrollTop200: false }
  },
  watch: {
    '$route' () {
      this.showSidebar = false
    },
    showSidebar (state) {
      if (state) {
        window.document.body.style.overflow = 'hidden'
      } else {
        window.document.body.style.removeProperty('overflow')
      }
    }
  },
  computed: {
    isLoggedIn () {
      return this.$store.getters['site/isLoggedIn']
    },
    user () {
      return this.$store.getters['site/identity']
    },

    showMobileMenu () {
      return ['PostCreate', 'PostView', 'SettingsProfile', 'SettingsPrivacy'].indexOf(this.$route.name) === -1 // this.$route.name !== 'PostCreate' && this.$route.name !== 'PostView';
    },

    activitiesBadge () {
      return this.$store.getters['site/getBadgeCounter']('badge')
    }
  },
  methods: {
    doScrollTop () {
      scrollToTop(1400)
    }
  },
  mounted () {
    let pageYOffsetPrev = 0
    window.addEventListener('scroll', () => {
      this.scrollTop200 = window.pageYOffset >= 200

      if (window.pageYOffset - pageYOffsetPrev >= 0) {
        pageYOffsetPrev = window.pageYOffset
        this.showNavbar = false
      } else if (window.pageYOffset - pageYOffsetPrev < -100) {
        pageYOffsetPrev = window.pageYOffset
        this.showNavbar = true
      }

      if (this.floatNavbar === false) {
        this.floatNavbar = window.innerHeight < window.pageYOffset
        if (this.floatNavbar) {
          setTimeout(() => {
            this.transitionNavbar = true
          }, 500)
        }
      } else if (window.pageYOffset === 0) {
        this.floatNavbar = false
        this.transitionNavbar = false
      }
    })
  }
}
</script>

<style scoped>
.navbar {
  box-shadow: 0 0 2px #343a40;
}

.menu-bottom {
  display: none;
}

@media (max-width: 767px) {
  .navbar {
    position: relative;
  }

  .float-navbar {
    padding-top: 50px;
  }

  .float-navbar .navbar {
    position: fixed;
    top: -50px;
  }

  .float-navbar.t .navbar {
    transition: all 0.3s ease-in-out;
  }

  .show-navbar.float-navbar .navbar {
    position: fixed;
    top: 0;
  }

  .menu-bottom {
    display: flex;
    transition: all 0.4s ease-in-out;
  }

  .float-navbar .menu-bottom {
    bottom: -50px;
  }

  .show-navbar.float-navbar .menu-bottom {
    bottom: 0;
  }

  .main-view {
    padding-bottom: 70px;
  }
}

.uncollapse {
  display: none;
}

.navbar-brand {
  margin-right: 8px;
}

.btn-notifications a {
  font-size: 18px;
  padding: 0;
  display: inline-block;
  width: 100%;
  text-align: center;
  height: 50px;
  line-height: 50px;
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

.nav-link_notifications {
  position: relative;
}

.nav-link_notifications .counter {
  position: absolute;
  font-size: 8px;
  top: 5px;
  right: 0;
  background: rgba(208, 0, 0, 0.9);
  color: white;
  border-radius: 5px;
  padding: 3px;
  min-width: 15px;
  text-align: center;
  line-height: 8px;
  animation: ncp 1s infinite;
}

.nav-item.nav-profile-item a {
  border-radius: 100%;
}

.nav-item.nav-profile-item.router-link-exact-active a {
  border: 2px solid white;
}

.menu-bottom {
  position: fixed;
  bottom: 0;
  z-index: 10;
}

@keyframes ncp {
  0% {
    background: rgba(208, 0, 0, 0.9);
  }
  50% {
    background: rgba(208, 0, 0, 1);
    box-shadow: 0 0 5px rgba(208, 0, 0, .8);
  }
  0% {
    background: rgba(208, 0, 0, 0.9);
  }
}
</style>
