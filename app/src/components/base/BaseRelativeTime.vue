<template>
    <span>{{ relative_time }}</span>
</template>

<script>
import moment from 'moment'

export default {
  name: 'base-relative-time',
  props: {
    time: {
      type: Number,
      required: true
    }
  },
  data () {
    return {
      relative_time: null
    }
  },
  watch: {
    time: {
      immediate: true,
      handler () {
        this.setup()
      }
    }
  },
  methods: {
    setup () {
      this.stopInterval()

      this.relative_time = moment.unix(this.time).fromNow()

      const diff = -(this.time - Math.round((new Date()).getTime() / 1000))

      if (diff < 600) {
        this._relativeTimeInterval = setInterval(() => this.relative_time = moment.unix(this.time).fromNow(), 10 * 1000)
      } else if (diff < 3600) {
        this._relativeTimeInterval = setInterval(() => this.relative_time = moment.unix(this.time).fromNow(), 45 * 1000)
      } else if (diff < 24 * 3600) {
        this._relativeTimeInterval = setInterval(() => this.relative_time = moment.unix(this.time).fromNow(), 300 * 1000)
      } else {
        this._relativeTimeInterval = null
      }
    },
    stopInterval () {
      if (this._relativeTimeInterval) clearInterval(this._relativeTimeInterval)
      this._relativeTimeInterval = null
    }
  },
  beforeDestroy () {
    this.stopInterval()
  }
}
</script>

<style scoped>

</style>
