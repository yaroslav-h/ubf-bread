<template>
    <div class="custom-control custom-checkbox">
        <input :id="id" type="checkbox" :checked="value" @input="onInput" class="custom-control-input" ref="input">
        <label class="custom-control-label" :for="id">{{ label }}</label>
    </div>
</template>

<script>
export default {
  name: 'base-form-checkbox',
  props: {
    id: {
      required: true,
      type: String
    },
    label: {
      required: false,
      type: String
    },
    value: {
      required: false
    }
  },
  methods: {
    onInput () {
      this.$emit('input', this.$refs.input.checked)
    }
  }
}
</script>

<style scoped>

</style>
