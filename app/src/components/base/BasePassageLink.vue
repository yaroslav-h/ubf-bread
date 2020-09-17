<template>
  <div>
    <a :href="link" target="_blank"><fa icon="book-open" class="mr-2"/>{{src}}</a>
    <!--<a :href="link" target="_blank"><fa icon="share-alt-square" class="ml-2"/></a>-->
  </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBookOpen, faShareAltSquare } from '@fortawesome/free-solid-svg-icons'
library.add(faBookOpen, faShareAltSquare)

export default {
  name: 'BasePassageLink',
  props: {
    src: {
      required: true
    },
    link: {
      required: false
    }
  }
}
</script>

<style scoped>
div {
  display: inline;
}
</style>
