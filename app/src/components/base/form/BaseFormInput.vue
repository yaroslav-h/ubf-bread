<template>
    <div>
        <input :id="id" :type="type" :value="value" :placeholder="placeholder" :disabled="disabled" :autocomplete="autocomplete" class="form-control" :class="{'is-invalid': error != null }" @input="onInput" ref="input">
        <small v-if="help" class="form-text text-muted">{{help}}</small>
        <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </div>
</template>

<script>
export default {
  name: 'base-form-input',
  props: {
    id: {
      required: false,
      type: String,
      default: ''
    },
    type: {
      required: false,
      type: String,
      default: 'text'
    },
    placeholder: {
      required: false,
      type: String,
      default: ''
    },
    value: {
      required: false
    },
    error: {
      required: false,
      type: String
    },
    disabled: {
      required: false,
      type: Boolean
    },
    help: {
      required: false,
      type: String
    },
    autocomplete: {
      required: false,
      type: String
    }
  },
  methods: {
    onInput () {
      this.$emit('input', this.$refs.input.value)
    }
  }
}
</script>

<style scoped>

</style>
