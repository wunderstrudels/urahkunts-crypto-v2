import Vue from 'vue';
import Vuex from 'vuex';

window.Vue = require('vue').default;
window.Vue.use(Vuex);

import user from './modules/user.js';
import wallet from './modules/wallet.js';
import graph from './modules/graph.js';

export const store = new Vuex.Store({
	modules: {
        user: user,
		wallet: wallet,
		graph: graph,
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