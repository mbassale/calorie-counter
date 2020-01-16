require('./bootstrap');

/**
 * Vue
 */
import Vue from 'vue';

/**
 * Bootstrap-Vue
 */
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

/**
 * Vuex Store
 */
import Vuex from 'vuex';
Vue.use(Vuex);
import Store from './store/index';
const store = new Vuex.Store(Store);

/**
 * Vue-Router
 */
import VueRouter from 'vue-router';
Vue.use(VueRouter);
import App from './App.vue';
import Dashboard from './views/dashboard/Dashboard.vue'
import Login from './views/auth/Login.vue';
import Register from './views/auth/Register.vue';
import ForgotPassword from './views/auth/ForgotPassword.vue';

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'dashboard',
            component: Dashboard
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
            path: '/forgot-password',
            name: 'forgotPassword',
            component: ForgotPassword
        }
    ],
});

// Authentication Guard
router.beforeEach((to, from, next) => {
    if (to.name !== 'login' && to.name !== 'register' && store.getters.isGuest) next('/login');
    else next();
});

const app = new Vue({
    el: '#app',
    render: (h) => h(App),
    store,
    router,
});
