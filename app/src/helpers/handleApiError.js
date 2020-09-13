export default function (err) {
  err.validation = {}
  err.msg = null

  if ('response' in err && err.response) {
    if (err.response.data) {
      err.msg = err.response.data.message || err.message
    }

    if (err.response.status === 403) {
      window.location.reload()
    } else if (err.response.status === 422) {
      err.validation = err.response.data
    } else {
      // store.dispatch('addError', {scope: 'window', info: 'fetch', error: err});
    }
  } else {
    // store.dispatch('addError', {scope: 'window', info: 'fetch', error: err});

    console.error('err handler', err) // eslint-disable-line no-use-before-define
  }

  return { data: null, err: err }
}
