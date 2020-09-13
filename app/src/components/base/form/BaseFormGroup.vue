<template>
    <div class="form-group">
        <label :for="labelFor" v-if="label" :class="labelClass"><slot name="label">{{ label }}</slot></label>
        <div>
            <slot name="default"></slot>
        </div>
        <div class="form-text text-muted" v-if="description">{{description}}</div>
    </div>
</template>

<script>
export default {
  name: 'base-form-group',
  props: {
    label: {
      require: false,
      type: String,
      default: ''
    },
    labelFor: {
      require: false,
      type: String,
      default: ''
    },
    description: {
      require: false,
      type: String,
      default: ''
    },
    labelClass: {
      require: false,
      type: String,
      default: ''
    }
  }
}
</script>

<style scoped>

</style>
