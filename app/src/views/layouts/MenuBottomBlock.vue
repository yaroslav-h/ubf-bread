<template>
  <div class="menu">
    <div>
      <router-link :to="{ name: 'Home' }" class="home">
        <fa icon="home"/>
      </router-link>
    </div>
    <div>
      <router-link :to="{ name: 'Calendar' }" class="calendar">
        <fa icon="calendar"/>
      </router-link>
    </div>
    <div>
      <router-link :to="{ name: 'Archive' }" class="archive">
        <fa icon="archive"/>
      </router-link>
    </div>
    <div v-if="isLoggedIn">
      <router-link :to="{ name: 'Profile' }" class="profile">
        <fa icon="user"/>
      </router-link>
    </div>
    <div v-else>
      <router-link :to="{ name: 'Login' }" class="login">
        <fa icon="sign-in-alt"/>
      </router-link>
    </div>
    <!--<div v-if="isLoggedIn">
      <router-link v-if="!isActivityPage" :to="{ name: 'Activity' }" class="activity">
        <fa icon="bell"/>
        <span class="counter"
              v-if="activitiesBadge.count > 0">{{ activitiesBadge.count > 99 ? '+99' : activitiesBadge.count }}</span>
      </router-link>
      <a v-else class="activity router-link-exact-active router-link-active" href="/activity"
         @click.prevent="doScrollTop">
        <fa icon="bell"/>
        <span class="counter"
              v-if="activitiesBadge.count > 0">{{ activitiesBadge.count > 99 ? '+99' : activitiesBadge.count }}</span>
      </a>
    </div>-->
    <!--<div>
        <router-link :to="{ name: 'Settings' }" class="settings">
            <fa icon="ellipsis-h"/>
        </router-link>
    </div>-->
  </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArchive, faHome, faCalendar, faSignInAlt, faSearch, faPlus, faBell, faUser, faEllipsisH } from '@fortawesome/free-solid-svg-icons'
import { scrollToTop } from '@/helpers/window'
library.add(faArchive, faHome, faCalendar, faSignInAlt, faSearch, faPlus, faBell, faUser, faEllipsisH)

export default {
  name: 'MenuBottomBlock',
  computed: {
    isLoggedIn () {
      return this.$store.getters['site/isLoggedIn']
    },
    user () {
      return this.$store.getters['site/identity']
    },
    activitiesBadge () {
      return this.$store.getters['site/getBadgeCounter']('badge')
    },
    isFeedPage () {
      return this.$route.name === 'Feed'
    },
    isActivityPage () {
      return this.$route.name === 'Activity'
    }
  },
  methods: {
    doScrollTop () {
      scrollToTop(1400)
    }
  }
}
</script>

<style scoped lang="scss">
.menu {
  width: 100%;
  height: 50px;
  background: white;
  display: flex;
  justify-content: space-between;

  border-top: 1px solid #f3f3f3; /*#eaeaea;*/

  > div {
    width: 100%;

    > a {
      display: flex;;
      width: 100%;
      height: 100%;
      align-items: center;
      justify-content: center;
      color: grey;
    }

    > a.archive.router-link-active,
    > a.settings.router-link-active,
    > a.router-link-exact-active {
      font-size: 20px;
      color: #007bff;
    }
  }
}

.profile {
  .uimg {
    width: 25px;
    height: 25px;
    padding: 0;
    filter: grayscale(1);
    transition: all .2s ease-in-out;
  }
}

.profile.router-link-active {
  .uimg {
    width: 32px;
    height: 32px;
    filter: grayscale(0);
  }
}

.activity {
  position: relative;
}

.activity .counter {
  position: absolute;
  font-size: 8px;
  top: 7px;
  right: calc(50% - 18px);
  background: rgba(208, 0, 0, 0.9);
  color: white;
  border-radius: 5px;
  padding: 3px;
  min-width: 15px;
  text-align: center;
  line-height: 8px;
  animation: ncp 1.5s infinite;
  border: 1px solid white;
}

@keyframes ncp {
  0% {
    background: rgba(208, 0, 0, 0.9);
  }
  50% {
    background: rgba(208, 0, 0, 1);
    box-shadow: 0 0 5px rgba(208, 0, 0, .1);
    border: 1px solid rgba(208, 0, 0, 1);
  }
  0% {
    background: rgba(208, 0, 0, 0.9);
  }
}
</style>
