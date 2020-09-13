function urlBase64ToUint8Array (base64String) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')

  const rawData = window.atob(base64)
  const outputArray = new Uint8Array(rawData.length)

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i)
  }
  return outputArray
}

function isSupported () {
  if (!('serviceWorker' in navigator)) {
    console.warn('Service workers are not supported by this browser')
    return false
  }
  if (!('PushManager' in window)) {
    console.warn('Push notifications are not supported by this browser')
    return false
  }
  if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
    console.warn('Notifications are not supported by this browser')
    return false
  }

  return true
}

function requestPermission () {
  return new Promise((resolve, reject) => {
    if (Notification.permission === 'denied') {
      return reject(new Error('Push messages are blocked.'))
    }

    if (Notification.permission === 'granted') {
      return resolve()
    }

    if (Notification.permission === 'default') {
      return Notification.requestPermission().then(result => {
        if (result !== 'granted') {
          reject(new Error('Bad permission result'))
        }
        resolve()
      })
    }
  })
}

function getSubscriptionInfo (subscription) {
  const key = subscription.getKey('p256dh')
  const token = subscription.getKey('auth')
  const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0]

  return {
    endpoint: subscription.endpoint,
    publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
    authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
    contentEncoding
  }
}

function subscribe (appServerKey, handler = () => {}) {
  return requestPermission()
    .then(() => navigator.serviceWorker.ready)
    .then(r =>
      r.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(appServerKey)
      })
    )
    .then(subscription => {
      // Subscription was successful
      // create subscription on your server
      return handler(subscription, getSubscriptionInfo(subscription))
    })
    .catch(e => {
      if (Notification.permission === 'denied') {
        // The user denied the notification permission which
        // means we failed to subscribe and the user will need
        // to manually change the notification permission to
        // subscribe to push messages
        console.warn('Notifications are denied by the user.')
      } else {
        // A problem occurred with the subscription; common reasons
        // include network errors or the user skipped the permission
        console.error('Impossible to subscribe to push notifications', e)
      }
    })
}

function subscription (handler = () => {}) {
  return navigator.serviceWorker.getRegistration()
    .then(r => {
      if (!r) {
        throw new Error('There is no SW!')
      }

      return r
    })
    .then(r => r.pushManager.getSubscription())
    .then(subscription => {
      if (!subscription) {
        throw new Error('No active subscription')
      }

      // Keep your server in sync with the latest endpoint
      return handler(subscription, getSubscriptionInfo(subscription))
    })
}

function unsubscribe (handler = () => {}) {
  return navigator.serviceWorker.ready
    .then(r => r.pushManager.getSubscription())
    .then(subscription => {
      if (!subscription) {
        throw new Error('No active subscription')
      }

      return handler(subscription, getSubscriptionInfo(subscription))
    })
}

export {
  isSupported,
  requestPermission,
  subscribe,
  subscription,
  unsubscribe
}
