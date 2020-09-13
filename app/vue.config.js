module.exports = {
  productionSourceMap: false,
  devServer: {
    proxy: {
      '^/_': {
        target: 'http://127.0.0.1:9001'
      },
      '^/upl': {
        target: 'http://127.0.0.1:9001'
      }
    }
  }
  // pwa: {
  //     name: 'Dev 0nefeed',
  //     themeColor: '#ffffff',
  //     msTileColor: '#ffffff',
  //     backgroundColor: '#ffffff',
  //     appleMobileWebAppCapable: 'yes',
  //     appleMobileWebAppStatusBarStyle: 'black',
  //     assetsVersion: 5,
  //     // configure the workbox plugin
  //     workboxPluginMode: 'InjectManifest',
  //     workboxOptions: {
  //         swSrc: 'src/service-worker.js',
  //         // ...other Workbox options...
  //     },
  //     manifestOptions: {
  //         // start_url: './offline.html',
  //         background_color: '#ffffff',
  //     }
  // }
}
