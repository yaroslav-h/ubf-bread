<template>
    <fa icon="circle-notch" spin />
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircleNotch } from '@fortawesome/free-solid-svg-icons'
library.add(faCircleNotch)

export default {
  name: 'base-fa-spinner'
}
</script>
