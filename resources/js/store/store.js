import Vue from 'vue';
import Vuex from 'vuex';

window.Vue = require('vue').default;
window.Vue.use(Vuex);

import user from './modules/user.js';

export const store = new Vuex.Store({
	modules: {
        user: user
	},
	state: {

	},
	getters: {

	},
	mutations: {

	},
	actions: {

	}
});