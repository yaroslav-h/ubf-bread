export function mapApiActions (vuexModuleName, actions) {
  const mappings = {}
  if (typeof vuexModuleName === 'object') {
    actions = vuexModuleName
    vuexModuleName = null
  }
  const isActionsArray = Array.isArray(actions)

  for (const [key, entry] of Object.entries(actions)) {
    let method, action, waiter
    if (entry === Object(entry)) {
      if (isActionsArray) {
        method = entry.action
        if (entry.method !== undefined) {
          method = entry.method
        }
      } else {
        method = key
      }
      action = entry.action
      waiter = entry.loader
    } else {
      if (isActionsArray) {
        method = action = entry
        waiter = entry
      } else {
        method = action = key
        waiter = entry
      }
    }
    if (!waiter) {
      waiter = action
    }
    if (action) {
      mappings[method] = async function (...args) {
        let loadingKey = waiter

        if (['number', 'string'].indexOf(typeof args[0]) > -1) {
          loadingKey = `${loadingKey}/${args[0]}`
        }

        if (typeof args[0] === 'object' && '_loading_key' in args[0]) {
          loadingKey = `${loadingKey}/${args[0]._loading_key}`
        }

        if (vuexModuleName) {
          loadingKey = `${vuexModuleName}/${loadingKey}`
        }

        if (this.$store.getters['loading/has'](loadingKey)) {
          return false
        }

        try {
          this.$store.dispatch('loading/start', loadingKey)
          return await this.$store.dispatch(
            vuexModuleName ? `${vuexModuleName}/${action}` : action,
            ...args
          )
        } finally {
          this.$store.dispatch('loading/end', loadingKey)
        }
      }
    }
  }
  return mappings
}

export function mapLoadingGetters (vuexModuleName, getters, key) {
  const mappings = {}

  if (typeof vuexModuleName === 'object') {
    getters = vuexModuleName
    vuexModuleName = null
  }

  Object.keys(getters).forEach(getter => {
    const waiter = getters[getter]

    mappings[getter] = function () {
      let loadingKey = waiter

      if (key) {
        if (key.indexOf('.') > 0) {
          const keySplitted = key.split('.')
          loadingKey = `${loadingKey}/${this[keySplitted[0]][keySplitted[1]]}`
        } else {
          loadingKey = `${loadingKey}/${this[key]}`
        }
      }

      if (vuexModuleName) {
        loadingKey = `${vuexModuleName}/${loadingKey}`
      }

      return this.$store.getters['loading/has'](loadingKey)
    }
  })
  return mappings
}
