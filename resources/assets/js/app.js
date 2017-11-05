
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('user',require('./components/user/index.vue'));
Vue.component('user',require('./components/user/create.vue'));

/**
 * VUE ROUTER
 */
import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'
import DataTables from 'vue-data-tables'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'

Vue.use(VueRouter)
Vue.use(ElementUI)
Vue.use(DataTables)

//define routes for users
const routes=[
   {
       path:'/',
       name:'Home',
       component:require('./components/home.vue')
   },
   {
       path:'/setting/status',
       name:'Status',
       component:require('./components/setting/status.vue')
   },
   {
       path:'/setting/golongan',
       name:'Golongan',
       component:require('./components/setting/golongan.vue')
   },
   {
       path:'/setting/pangkat',
       name:'Pangkat',
       component:require('./components/setting/pangkat.vue')
   },
   {
       path:'/pegawai',
       name:'Pegawai',
       component:require('./components/pegawai/index.vue')
   },
   {
       path:'/sasaran-kerja',
       name:'Sasarankerja',
       component:require('./components/sasaran/index.vue')
   }
]

const router=new VueRouter({routes});

const app = new Vue({
  router
}).$mount('#main');