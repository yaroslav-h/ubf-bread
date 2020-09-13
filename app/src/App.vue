<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<style lang="scss">
@import "node_modules/bootstrap/scss/bootstrap.scss";
.ws-nw {
  white-space: nowrap;
}
body {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  overflow-y: scroll;
  padding-top: 56px;
  min-width: 300px;
}
.container {
  max-width: 720px;
  padding: 0;
}
@media (max-width: 767px) {
  body {
    padding-top: 0px !important;
  }
  .col-sm-12 {
    padding: 0 8px;
  }
  .col-full {
    padding: 0;
  }
  .list-group-item:first-child {
    border-top: 0;
  }
  .list-group-item:last-child {
    border-bottom: 0;
  }
  .list-group-item {
    border-radius: 0 !important;
    border-left: 0;
    border-right: 0;
  }
  .col-sm-12.mt-4 {
    margin-top: .25rem !important;
  }

  .card {
    border-radius: 0;
  }
  .card.mt-2 {
    margin-top: 0 !important;
  }
}
</style>
