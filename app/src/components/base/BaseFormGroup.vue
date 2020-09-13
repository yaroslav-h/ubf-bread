<template>
    <div class="form-group" v-if="isBoolInput">
        <div class="custom-control custom-checkbox">
            <input :checked="value" @input="onInput" :type="type" class="custom-control-input" :id="id" ref="input">
            <label class="custom-control-label" :for="id">{{ label }} <slot name="label"></slot></label>
        </div>
    </div>
    <div class="form-group" v-else>
        <label v-if="label" :for="id">{{ label }} <slot name="label"></slot></label>
        <input :type="type" :id="id" :value="value" @input="onInput" :class="{'is-invalid': error != null }" class="form-control" ref="input">
        <small v-if="help" class="form-text text-muted">{{help}}</small>
        <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </div>
</template>

<script>
export default {
  name: 'base-form-group',
  props: {
    id: {
      required: false,
      type: String,
      default: () => {
        let text = ''
        const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'

        for (let i = 0; i <= 10; i++) { text += possible.charAt(Math.floor(Math.random() * possible.length)) }

        return text
      }
    },
    label: {
      required: false,
      type: String
    },
    type: {
      required: false,
      type: String,
      default: 'text'
    },
    value: {
      required: false
    },
    error: {
      required: false,
      type: String
    },
    help: {
      required: false,
      type: String
    }
  },
  computed: {
    isBoolInput () {
      return ['checkbox', 'radio'].indexOf(this.type) >= 0
    }
  },
  methods: {
    onInput () {
      if (this.isBoolInput) {
        this.$emit('input', this.$refs.input.checked)
      } else {
        this.$emit('input', this.$refs.input.value)
      }
    }
  }
}
</script>

<style scoped>
    label {
        width: 100%;
        user-select: none;
    }
</style>
