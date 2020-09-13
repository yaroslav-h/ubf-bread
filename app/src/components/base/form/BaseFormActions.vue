<template>
    <div class="btn-group">
        <slot name="default"></slot>
    </div>
</template>

<script>
export default {
  name: 'base-form-actions'
}
</script>

<style scoped>
    .btn-group {
        width: 100%;
    }
</style>
