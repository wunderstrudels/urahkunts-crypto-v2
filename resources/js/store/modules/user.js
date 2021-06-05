export default {
	namespaced: true,
	state: {
		authenticated: false,
		data: null,
		auth: (JSON.parse(localStorage.getItem("auth")) != null) ? JSON.parse(localStorage.getItem("auth")) : null
	},
	getters: {
        authenticated(state) {
			return state.authenticated;
		},
		auth(state) {
			return state.auth;
		},
		data(state) {
			return state.data;
		}
	},
	mutations: {
        authenticated(state, data) {
			state.authenticated = data;
		},
		auth(state, data) {
			state.auth = data;
		},
		data(state, data) {
			state.data = data;
		}
	},
	actions: {
		login(context, data) {
			return new Promise((resolve, reject) => {
				axios.post(window.web + '/login', data).then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}

					context.commit("data", response.data.user);
					context.commit("auth", response.data.auth);
					localStorage.setItem("auth", JSON.stringify(response.data.auth));

					context.commit("authenticated", true);

					resolve(response);
				}).catch(error => {
					context.commit("authenticated", false);
					reject(error);
				});
			});
		},
		read(context, data = null) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/user').then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
					
					context.commit("data", response.data);
					context.commit("authenticated", true);
					resolve(response);
				}).catch(error => {
					context.commit("authenticated", false);
					reject(error);
				});
			});
		},
	}
};