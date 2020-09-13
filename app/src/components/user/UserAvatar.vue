<template>
  <div class="avatar" :style="{width: size+'px', height: size+'px', fontSize: (size/2)+'px'}">
    {{ chars }}
  </div>
</template>

<script>
export default {
  name: 'UserAvatar',
  props: {
    user: {
      required: true
    },
    size: {
      required: false,
      default: '50'
    }
  },
  computed: {
    chars () {
      return this.user.name.substr(0, 2).toUpperCase()
    }
  }
}
</script>

<style lang="scss" scoped>
.avatar {
  border-radius: 100%;
  background: grey;
  color: white;
  font-size: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
