
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')
const store = require('./store.js')

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const routes = require('./routes.js')
// Vue.transition('fade', { enterClass: 'fadeIn', leaveClass: 'fadeOut'})
Vue.component('AppComponent', require('./components/App.vue'));
const Vuex = require('vuex').default

const router = new VueRouter({
  linkActiveClass: 'is-active',
  routes // short for routes: routes
})

// router.beforeEach(function () {  
//   window.scrollTo(0, 0)
// })

const app = new Vue({
  router,
  store: new Vuex.Store(store)
}).$mount('#app');
