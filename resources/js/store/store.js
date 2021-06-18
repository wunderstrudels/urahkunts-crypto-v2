import Vue from 'vue';
import Vuex from 'vuex';

window.Vue = require('vue').default;
window.Vue.use(Vuex);

import user from './modules/user.js';
import currency from './modules/currency.js';
import scenario from './modules/scenario.js';
import wallet from './modules/wallet.js';
import bot from './modules/bot.js';
import graph from './modules/graph.js';

export const store = new Vuex.Store({
	modules: {
        user: user,
		currency: currency,
		scenario: scenario,
		wallet: wallet,
		bot: bot,
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