/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap');
 import VueRouter from 'vue-router';
 import Vuex from 'vuex';
 
 import {router} from './router/router.js';
 import {store} from './store/store.js';
 
 
 window.Vue = require('vue').default;
 window.Vue.use(VueRouter);
 
 
 // Set urls for requests.
 window.web = "https://v2.johnbob.dk";
 window.api = "https://v2.johnbob.dk/api";
 window.path = window.location.pathname;
 
 
 if(store.getters["user/auth"] != null) {
     axios.defaults.headers.common["Authorization"] = "Bearer " + store.getters["user/auth"].access_token;
 
     store.dispatch("user/read").then(() => {
        router.push(window.path);
     });
 }
 
 
 /**
  * The following block of code may be used to automatically register your
  * Vue components. It will recursively scan this directory for the Vue
  * components and automatically register them with their "basename".
  *
  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
  */
 
 // const files = require.context('./', true, /\.vue$/i)
 // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
 
 Vue.component('master', require('./components/Master.vue').default);
 
 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */
 
 
 
 // Handle router before/after.
 router.beforeEach((to, from, next) => {
     if(to.meta.requiresAuth == false) {
         return next();
     }
 
     if(store.getters["user/authenticated"] == false) {
         return next("login");
     }
     
     return next();
 });
 
 
 // Create app.
 const app = new Vue({
     el: '#app',
     router: router,
     store: store
 });
 













 // Helpers.
 window.helpers = {
    date: (hours = 0, days = 0, months = 0, years = 0) => {
        const now = new Date();

        const output = new Date();
        output.setFullYear(now.getFullYear() + years);
        output.setMonth(now.getMonth() + (months + 1));
        output.setDate(now.getDate() + days);
        output.setHours(now.getHours() + hours);

        let month = ("0" + output.getMonth()).slice(-2);
        let date = ("0" + output.getDate()).slice(-2);
        let hour = ("0" + output.getHours()).slice(-2);
        let minute = ("0" + output.getMinutes()).slice(-2);
        let second = ("0" + output.getSeconds()).slice(-2);

        return `${output.getFullYear()}-${month}-${date} ${hour}:${minute}:${second}`;
    }
 };