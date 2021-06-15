export default {
	namespaced: true,
	state: {
        list: []
	},
	getters: {
        list(state) {
            return state.list;
        }
	},
	mutations: {
        list(state, data) {
            state.list = data;
        }
	},
	actions: {
        list(context, data) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/currencies').then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
                    context.commit("list", response.data);
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		},
	}
};